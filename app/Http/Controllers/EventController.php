<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSponsorship;
use App\Models\SponsorshipProgram;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date'        => ['required', 'date'], // single datetime column
            'location'    => ['nullable', 'string', 'max:255'],

            // optional initial sponsorship
            'sponsor_id'  => ['nullable', 'exists:users,id'],
            'amount'      => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $event = Event::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'date'        => $data['date'],
            'location'    => $data['location'] ?? null,
        ]);

        if (!empty($data['sponsor_id']) && !empty($data['amount'])) {
            $event->sponsorships()->create([
                'sponsor_id' => $data['sponsor_id'],
                'amount'     => $data['amount']
            ]);
        }

        return redirect()->route('events.show', $event)->with('success', 'Event created successfully.');
    }

    public function update(Request $r, Event $event)
    {
        $data = $r->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date'        => ['required', 'date'],
            'location'    => ['nullable', 'string', 'max:255'],
            'sponsor_id'  => ['nullable', 'exists:users,id'],
            'amount'      => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $event->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'date'        => $data['date'],
            'location'    => $data['location'] ?? null,
        ]);

        if (!empty($data['sponsor_id']) && !empty($data['amount'])) {
            $event->sponsorships()->create([
                'sponsor_id' => $data['sponsor_id'],
                'amount'     => $data['amount'],
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
}
