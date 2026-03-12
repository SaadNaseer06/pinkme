<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSponsorship;
use App\Models\SponsorshipProgram;
use App\Models\User;
use App\Mail\EventRegistrationStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['sponsors'])
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $displayCol = $this->userDisplayColumn();

        // select only columns that exist
        $sponsors = User::query()
            ->where('role_id', 3)
            ->orderBy($displayCol)
            ->get(['id', $displayCol, 'email']);

        return view('admin.events.create', compact('sponsors', 'displayCol'));
    }


    public function edit(Event $event)
    {
        $displayCol = $this->userDisplayColumn();

        $sponsors = User::query()
            ->where('role_id', 3)
            ->orderBy($displayCol)
            ->get(['id', $displayCol, 'email']);

        return view('admin.events.create', compact('event', 'sponsors', 'displayCol'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'start_date'            => ['required', 'date'],
            'end_date'              => ['nullable', 'date', 'after_or_equal:start_date'],
            'location'              => ['nullable', 'string', 'max:255'],
            'funding_goal'          => ['nullable', 'numeric', 'min:0'],
            'payment_type'          => ['required', 'in:full,flexible'],
            'event_highlights'      => ['nullable', 'string'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // optional initial sponsorship
            'sponsor_id'            => ['nullable', 'exists:users,id'],
            'amount'                => ['nullable', 'numeric', 'min:0.01'],
        ]);

        // Handle image upload
        $imagePath = null;
        if ($r->hasFile('image')) {
            $imagePath = $r->file('image')->store('events', 'public');
        }

        $status = $this->resolveEventStatus(null, $data['start_date'], $data['end_date'] ?? null);

        $event = Event::create([
            'title'                 => $data['title'],
            'description'           => $data['description'] ?? null,
            'date'                  => $data['start_date'],
            'location'              => $data['location'] ?? null,
            'funding_goal'          => $data['funding_goal'] ?? null,
            'payment_type'          => $data['payment_type'],
            'status'                => $status,
            'event_highlights'      => $data['event_highlights'] ?? null,
            'image'                 => $imagePath,
            'registration_deadline' => $data['end_date'] ?? null,
        ]);

        if (!empty($data['sponsor_id']) && !empty($data['amount'])) {
            $event->sponsorships()->create([
                'sponsor_id'         => $data['sponsor_id'],
                'amount'             => $data['amount'],
                'registration_status' => 'confirmed',
                'registered_at'      => now(),
                'confirmed_at'       => now(),
            ]);
        }

        return redirect()->route('admin.programs-events', $event)->with('success', 'Event created successfully.');
    }

    public function update(Request $r, Event $event)
    {
        $data = $r->validate([
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'start_date'            => ['required', 'date'],
            'end_date'              => ['nullable', 'date', 'after_or_equal:start_date'],
            'location'              => ['nullable', 'string', 'max:255'],
            'funding_goal'          => ['nullable', 'numeric', 'min:0'],
            'payment_type'          => ['required', 'in:full,flexible'],
            'status'                => ['nullable', 'in:cancelled'],
            'event_highlights'      => ['nullable', 'string'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // optional additional sponsorship
            'sponsor_id'            => ['nullable', 'exists:users,id'],
            'amount'                => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $status = $this->resolveEventStatus($data['status'] ?? null, $data['start_date'], $data['end_date'] ?? null);

        // Handle image upload
        $updateData = [
            'title'                 => $data['title'],
            'description'           => $data['description'] ?? null,
            'date'                  => $data['start_date'],
            'location'              => $data['location'] ?? null,
            'funding_goal'          => $data['funding_goal'] ?? null,
            'payment_type'          => $data['payment_type'],
            'status'                => $status,
            'event_highlights'      => $data['event_highlights'] ?? null,
            'registration_deadline' => $data['end_date'] ?? null,
        ];

        if ($r->hasFile('image')) {
            // Delete old image if exists
            if ($event->image && \Storage::disk('public')->exists($event->image)) {
                \Storage::disk('public')->delete($event->image);
            }
            $updateData['image'] = $r->file('image')->store('events', 'public');
        }

        $event->update($updateData);

        if (!empty($data['sponsor_id']) && !empty($data['amount'])) {
            $event->sponsorships()->create([
                'sponsor_id'         => $data['sponsor_id'],
                'amount'             => $data['amount'],
                'registration_status' => 'confirmed',
                'registered_at'      => now(),
                'confirmed_at'       => now(),
            ]);
        }

        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully.');
    }

    public function show(Event $event)
    {
        $displayCol = $this->userDisplayColumn();
        $event->load(['sponsorships.sponsor']);
        $sponsors = User::orderBy($displayCol)->get(['id', $displayCol, 'email']);

        return view('admin.events.show', compact('event', 'sponsors'));
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.sponsors')->with('success', 'Event deleted successfully.');
    }

    public function storeSponsorship(Request $r, Event $event)
    {
        $data = $r->validate([
            'sponsor_id' => ['required', 'exists:users,id'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
        ]);

        $event->sponsorships()->create($data);

        return back()->with('success', 'Sponsorship recorded.');
    }

    public function destroySponsorship(Event $event, EventSponsorship $sponsorship)
    {
        // simple safety: ensure the sponsorship belongs to this event
        abort_unless($sponsorship->event_id === $event->id, 404);

        $sponsorship->delete();

        return back()->with('success', 'Sponsorship removed.');
    }

    private function userDisplayColumn(): string
    {
        foreach (['name', 'full_name', 'username'] as $col) {
            if (Schema::hasColumn('users', $col)) {
                return $col;
            }
        }
        return 'email'; // guaranteed to exist
    }

    private function resolveEventStatus(?string $forcedStatus, ?string $startDate, ?string $endDate): string
    {
        if ($forcedStatus === 'cancelled') {
            return 'cancelled';
        }

        $start = $startDate ? \Carbon\Carbon::parse($startDate) : null;
        $end = $endDate ? \Carbon\Carbon::parse($endDate) : $start;

        if (!$start) {
            return 'upcoming';
        }

        if (now()->lt($start)) {
            return 'upcoming';
        }

        if ($end && now()->gt($end)) {
            return 'completed';
        }

        return 'ongoing';
    }
    
    /**
     * Show all event registration requests for admin approval
     */
    public function registrations(Request $request)
    {
        $displayCol = $this->userDisplayColumn();
        $selectedEventId = (int) $request->query('event_id');
        if ($selectedEventId <= 0) {
            $selectedEventId = null;
        }

        $pendingRegistrations = EventSponsorship::with(['event', 'sponsor'])
            ->when($selectedEventId, fn($query) => $query->where('event_id', $selectedEventId))
            ->where('registration_status', 'pending')
            ->orderBy('registered_at', 'desc')
            ->get();
            
        $allRegistrations = EventSponsorship::with(['event', 'sponsor'])
            ->when($selectedEventId, fn($query) => $query->where('event_id', $selectedEventId))
            ->orderBy('registered_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $eventsForFilter = Event::orderBy('title')->get(['id', 'title']);
            
        return view('admin.events.registrations', [
            'pendingRegistrations' => $pendingRegistrations,
            'allRegistrations'     => $allRegistrations,
            'displayCol'           => $displayCol,
            'eventsForFilter'      => $eventsForFilter,
            'selectedEventId'      => $selectedEventId,
        ]);
    }
    
    /**
     * Approve an event sponsorship registration
     */
    public function approveRegistration(EventSponsorship $registration)
    {
        if (!$registration->canBeApproved()) {
            return back()->with('error', 'This registration cannot be approved.');
        }
        
        $registration->approve();

        $registration->loadMissing(['event', 'sponsor.profile']);
        if ($registration->sponsor?->email) {
            Mail::to($registration->sponsor->email)->queue(new EventRegistrationStatus($registration, 'Approved'));
        }
        
        return back()->with('success', 'Event registration approved successfully!');
    }
    
    /**
     * Reject an event sponsorship registration
     */
    public function rejectRegistration(EventSponsorship $registration)
    {
        if (!$registration->canBeRejected()) {
            return back()->with('error', 'This registration cannot be rejected.');
        }
        
        $registration->reject();

        $registration->loadMissing(['event', 'sponsor.profile']);
        if ($registration->sponsor?->email) {
            Mail::to($registration->sponsor->email)->queue(new EventRegistrationStatus($registration, 'Rejected'));
        }
        
        return back()->with('success', 'Event registration rejected.');
    }
}
