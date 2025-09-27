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

    public function show($id)
    {
        $program = Program::with('sponsor')->findOrFail($id);

        return response()->json([
            'title' => $program->title,
            'description' => $program->description,
            'event_date' => \Carbon\Carbon::parse($program->event_date)->format('l, F d, Y'),
            'event_time' => $program->event_time,
            'banner' => asset('storage/' . $program->banner),
            'sponsor' => [
                'name' => $program->sponsor->name ?? 'N/A',
                'phone' => $program->sponsor->phone ?? 'N/A',
                'email' => $program->sponsor->email ?? 'N/A',
                'logo' => asset('storage/' . ($program->sponsor->logo ?? 'default.png')),
                'about' => $program->sponsor->about ?? 'No details available.',
            ],
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
            'status'      => ['required', 'in:upcoming,ongoing,completed'],\n            'program_fund' => ['required', 'numeric', 'min:0'],
            'banner'      => ['nullable', 'image', 'max:2048'],
        ]);

        if ($r->hasFile('banner')) {
            $data['banner'] = $r->file('banner')->store('programs', 'public');
        }

        Program::create($data);

        return back()->with('success', 'Program created.');
    }
}
