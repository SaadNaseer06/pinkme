<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminCaseManagerController extends Controller
{
    public function index()
    {
        $caseManagers = User::whereHas('role', fn($q) => $q->where('name', 'casemanager'))
            ->with('profile')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.case_managers.index', compact('caseManagers'));
    }

    public function create()
    {
        return view('admin.case_managers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:255', 'unique:user_profiles,username'],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ]);

        $roleId = Role::where('name', 'casemanager')->value('id');

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => trim($request->first_name . ' ' . $request->last_name),
            'phone' => $request->phone,
            'username' => $request->username,
            'status' => (int)$request->status,
        ]);

        return redirect()->route('admin.case-managers.index')
            ->with('success', 'Case manager created successfully.');
    }

    public function edit(User $case_manager)
    {
        // Route model binding uses parameter name case_manager from resource
        if ($case_manager->role?->name !== 'casemanager') {
            abort(404);
        }
        $user = $case_manager->load('profile');
        return view('admin.case_managers.edit', compact('user'));
    }

    public function update(Request $request, User $case_manager)
    {
        if ($case_manager->role?->name !== 'casemanager') {
            abort(404);
        }

        $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($case_manager->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('user_profiles', 'username')->ignore($case_manager->id, 'user_id')],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ]);

        // Update user table
        $case_manager->email = $request->email;
        if ($request->filled('password')) {
            $case_manager->password = Hash::make($request->password);
        }
        $case_manager->save();

        // Update or create profile
        $profile = $case_manager->profile ?: new UserProfile(['user_id' => $case_manager->id]);
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->full_name = trim($request->first_name . ' ' . $request->last_name);
        $profile->phone = $request->phone;
        $profile->username = $request->username;
        $profile->status = (int)$request->status;
        $profile->save();

        return redirect()->route('admin.case-managers.index')
            ->with('success', 'Case manager updated successfully.');
    }
}
