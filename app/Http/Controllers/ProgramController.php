<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use App\Models\SponsorshipProgram;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index()
    {
        $upcomingPrograms = Program::where('status', 'upcoming')->get();
        $ongoingPrograms = Program::where('status', 'ongoing')->get();

        return view('patient.programs.index', compact('upcomingPrograms', 'ongoingPrograms'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|string|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ]);

        $validated['user_id'] = Auth::id();

        ProgramRegistration::create($validated);

        return back()->with('success', 'You have successfully registered for the program.');
    }

    public function show(Request $request, $id)
    {
        // Fetch the program; do not eager-load a nonexistent 'sponsor' relation
        $program = Program::findOrFail($id);

        $registration = null;
        if (Auth::check()) {
            $registration = ProgramRegistration::where('program_id', $program->id)
                ->where('user_id', Auth::id())
                ->select(['id', 'program_id', 'status', 'created_at', 'review_note'])
                ->first();
        }

        // Resolve a sponsor from the most recent sponsorship record, if any
        $latestSponsorship = $program->sponsorships()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->with(['sponsor.sponsorDetail'])
            ->first();

        $sponsorUser = $latestSponsorship?->sponsor->profile;
        $sponsorDetail = $sponsorUser?->sponsorDetail;

        $sponsorPayload = [
            'name'  => $sponsorDetail->company_name ?? ($sponsorUser?->full_name ?? 'N/A'),
            'phone' => $sponsorDetail->company_phone ?? ($sponsorUser?->phone ?? 'N/A'),
            'email' => $sponsorDetail->company_email ?? ($sponsorUser?->email ?? 'N/A'),
            'logo'  => $sponsorUser && $sponsorUser->avatar
                ? asset('storage/' . $sponsorUser->avatar)
                : asset('images/default_sponsor.png'),
            'about' => $sponsorDetail->company_type ?? 'No details available.',
        ];

        $registrationPayload = $registration ? [
            'id' => $registration->id,
            'status' => $registration->status,
            'status_label' => $registration->status_label,
            'submitted_at' => optional($registration->created_at)->format('d M Y, h:i A'),
            'view_url' => route('patient.programRegistrations.show', $registration),
            'review_note' => $registration->review_note,
        ] : null;

        if (!$request->expectsJson()) {
            if ($registration) {
                return redirect()->route('patient.programRegistrations.show', $registration);
            }

            return redirect()->route('patient.programsAndAids')
                ->with('info', 'You have not registered for this program yet.');
        }

        return response()->json([
            'title' => $program->title,
            'description' => $program->description,
            'event_date' => \Carbon\Carbon::parse($program->event_date)->format('l, F d, Y'),
            'event_time' => $program->event_time,
            'banner' => $program->banner ? asset('storage/' . $program->banner) : asset('images/default_program_banner.png'),
            'sponsor' => $sponsorPayload,
            'registration' => $registrationPayload,
        ]);
    }

    public function create()
    {
        return view('admin.programs.create');
    }

    public function store(Request $r)
    {
        // Match your programs migration exactly
        $data = $r->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date'  => ['required', 'date'],
            'event_time'  => ['required', 'date_format:H:i'],
'status'      => ['required', 'in:upcoming,ongoing,completed'],
            'payment_type' => ['required', 'in:full,flexible'],
            'program_fund' => ['required', 'numeric', 'min:0'],
            'banner'      => ['nullable', 'image', 'max:2048'],
        ]);

        if ($r->hasFile('banner')) {
            $data['banner'] = $r->file('banner')->store('programs', 'public');
        }

        Program::create($data);

        return back()->with('success', 'Program created.');
    }

    public function edit(Program $program)
    {
        // Render the edit form
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $r, Program $program)
    {
        $data = $r->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date'  => ['required', 'date'],
            'event_time'  => ['required', 'date_format:H:i'],
'status'      => ['required', 'in:upcoming,ongoing,completed'],
            'payment_type' => ['required', 'in:full,flexible'],
            'program_fund' => ['required', 'numeric', 'min:0'],
            'banner'      => ['nullable', 'image', 'max:2048'],
        ]);

        if ($r->hasFile('banner')) {
            $data['banner'] = $r->file('banner')->store('programs', 'public');
        } else {
            // Keep existing banner if not replaced
            unset($data['banner']);
        }

        $program->update($data);

        return redirect()->route('programs.edit', $program)->with('success', 'Program updated.');
    }
}
