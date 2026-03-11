<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminFinanceUserController extends Controller
{
    public function index()
    {
        $financeUsers = User::whereHas('role', fn($q) => $q->where('name', 'finance'))
            ->with('profile')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.finance_users.index', compact('financeUsers'));
    }

    public function create()
    {
        return view('admin.finance_users.create');
    }

    public function store(Request $request)
    {
        $request->merge(['username' => $request->filled('username') ? trim($request->username) : null]);

        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:255', 'unique:user_profiles,username'],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ]);

        $roleId = Role::where('name', 'finance')->value('id');
        if (!$roleId) {
            return redirect()->back()->withInput()->withErrors(['email' => 'Finance role not found. Please run: php artisan db:seed --class=RoleSeeder']);
        }

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
            'status' => (int) $request->status,
        ]);

        return redirect()->route('admin.finance-users.index')
            ->with('success', 'Finance user created successfully.');
    }

    public function edit(User $finance_user)
    {
        if ($finance_user->role?->name !== 'finance') {
            abort(404);
        }
        $user = $finance_user->load('profile');
        return view('admin.finance_users.edit', compact('user'));
    }

    public function update(Request $request, User $finance_user)
    {
        if ($finance_user->role?->name !== 'finance') {
            abort(404);
        }

        $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($finance_user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('user_profiles', 'username')->ignore($finance_user->id, 'user_id')],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ]);

        $finance_user->email = $request->email;
        if ($request->filled('password')) {
            $finance_user->password = Hash::make($request->password);
        }
        $finance_user->save();

        $profile = $finance_user->profile ?: new UserProfile(['user_id' => $finance_user->id]);
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->full_name = trim($request->first_name . ' ' . $request->last_name);
        $profile->phone = $request->phone;
        $profile->username = $request->filled('username') ? trim($request->username) : null;
        $profile->status = (int) $request->status;
        $profile->save();

        return redirect()->route('admin.finance-users.index')
            ->with('success', 'Finance user updated successfully.');
    }
}
