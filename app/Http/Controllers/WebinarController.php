<?php

namespace App\Http\Controllers;

use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebinarController extends Controller
{
    public function index()
    {
        $webinars = Webinar::query()
            ->withCount([
                'registrations as attendee_count' => fn($q) => $q->where('status', 'registered'),
                'registrations as sponsor_count' => fn($q) => $q
                    ->where('status', 'registered')
                    ->where('role_name', 'sponsor'),
                'registrations as patient_count' => fn($q) => $q
                    ->where('status', 'registered')
                    ->where('role_name', 'patient'),
            ])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('admin.webinars.index', compact('webinars'));
    }

    public function create()
    {
        $webinar = new Webinar([
            'status' => 'upcoming',
            'scheduled_at' => now()->addDays(1),
        ]);

        return view('admin.webinars.create', compact('webinar'));
    }

    public function store(Request $request)
    {
        $data = $this->validateWebinar($request);
        $data['created_by'] = Auth::id();

        $webinar = Webinar::create($data);

        return redirect()
            ->route('admin.webinars.show', $webinar)
            ->with('success', 'Webinar created successfully.');
    }

    public function show(Webinar $webinar)
    {
        $webinar->load(['registrations.user.profile']);

        return view('admin.webinars.show', compact('webinar'));
    }

    public function edit(Webinar $webinar)
    {
        return view('admin.webinars.create', compact('webinar'));
    }

    public function update(Request $request, Webinar $webinar)
    {
        $data = $this->validateWebinar($request);

        $webinar->update($data);

        return redirect()
            ->route('admin.webinars.show', $webinar)
            ->with('success', 'Webinar updated successfully.');
    }

    public function destroy(Webinar $webinar)
    {
        $webinar->delete();

        return redirect()
            ->route('admin.webinars.index')
            ->with('success', 'Webinar deleted successfully.');
    }

    private function validateWebinar(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'presenter' => ['nullable', 'string', 'max:255'],
            'join_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['required', 'in:upcoming,live,completed,cancelled'],
            'audience' => ['required', 'in:both,patient,sponsor'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}
