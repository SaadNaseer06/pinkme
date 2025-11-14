<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Application;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request, User $contact): JsonResponse
    {
        $user = $request->user();
        $this->authorizeConversation($user, $contact);

        Message::markThreadAsRead($user->id, $contact->id);

        $query = Message::betweenUsers($user->id, $contact->id)
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('sent_at');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('attachment_name', 'like', "%{$search}%")
                  ->orWhere('attachment_mime', 'like', "%{$search}%");
            });
        }

        $messages = $query->limit(200)
            ->get()
            ->map->toFrontendPayload()
            ->values();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, User $contact): JsonResponse
    {
        $user = $request->user();
        $this->authorizeConversation($user, $contact);

        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'max:5120'], // 5 MB
        ]);

        if (!$request->hasFile('attachment') && !filled($data['content'] ?? null)) {
            return response()->json(['error' => 'Message content or an attachment (max 5 MB) is required.'], 422);
        }

        $attachmentMeta = [
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_size' => null,
            'attachment_mime' => null,
        ];

        if ($file = $request->file('attachment')) {
            $stored = $file->store('chat_attachments', 'public');
            $attachmentMeta = [
                'attachment_path' => $stored,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_size' => $file->getSize(),
                'attachment_mime' => $file->getMimeType(),
            ];
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $contact->id,
            'content' => $data['content'] ?? '',
            ...$attachmentMeta,
            'is_read' => false,
            'sent_at' => now(),
        ])->load(['sender.profile', 'receiver.profile']);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => $message->toFrontendPayload(),
        ], 201);
    }

    protected function authorizeConversation(User $authUser, User $contact): void
    {
        $authRole = optional($authUser->role)->name;
        $contactRole = optional($contact->role)->name;

        if ($authRole === 'patient') {
            if (!in_array($contactRole, ['admin', 'casemanager'], true)) {
                abort(403);
            }

            if ($contactRole === 'casemanager') {
                $hasRelationship = Application::whereHas('patient', function ($query) use ($authUser) {
                    $query->where('user_id', $authUser->id);
                })->where('reviewer_id', $contact->id)->exists();

                if (! $hasRelationship) {
                    abort(403);
                }
            }

            return;
        }

        if ($authRole === 'casemanager') {
            if ($contactRole !== 'patient') {
                abort(403);
            }

            $hasRelationship = Application::where('reviewer_id', $authUser->id)
                ->whereHas('patient', function ($query) use ($contact) {
                    $query->where('user_id', $contact->id);
                })->exists();

            if (! $hasRelationship) {
                abort(403);
            }

            return;
        }

        if ($authRole === 'admin') {
            if (!in_array($contactRole, ['patient', 'casemanager', 'sponsor', 'admin'], true)) {
                abort(403);
            }

            return;
        }

        abort(403);
    }
}
