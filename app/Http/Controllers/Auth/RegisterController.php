<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SponsorDetail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.signup_login', [
            'rememberedLogin' => request()->cookie('remembered_login'),
            'rememberedPassword' => request()->cookie('remembered_password'),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate($this->rules($request));

        $email = $validated['email'] ?? $validated['company_email'] ?? null;
        $profileName = $validated['full_name'] ?? $validated['company_name'] ?? null;
        $phone = $validated['phone'] ?? $validated['company_phone'] ?? null;

        $user = User::create([
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'full_name' => $profileName,
            'phone' => $phone,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ]);

        if (($validated['sponsor_mode'] ?? null) === 'company') {
            $logoPath = $request->file('logo')
                ? $request->file('logo')->store('logos', 'public')
                : null;

            SponsorDetail::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'registration_number' => $validated['registration_number'],
                'company_email' => $validated['company_email'],
                'company_phone' => $validated['company_phone'],
                'company_type' => $validated['company_type'],
                'logo' => $logoPath,
            ]);
        }

        return redirect()->route('register', ['tab' => 'login'])
            ->with('success', 'Registration successful. Please log in.');
    }

    protected function rules(Request $request)
    {
        $rules = [
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ];

        $roleId = (int) $request->input('role_id');

        if ($roleId === 2) {
            $rules += [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'string', 'max:50'],
                'date_of_birth' => ['nullable', 'date'],
                'gender' => ['nullable', Rule::in(['female', 'male', 'other'])],
            ];
        } elseif ($roleId === 3) {
            $rules += [
                'sponsor_mode' => ['required', Rule::in(['individual', 'company'])],
            ];

            if ($request->input('sponsor_mode') === 'company') {
                $rules += [
                    'company_name' => ['required', 'string', 'max:255'],
                    'company_email' => ['required', 'email', 'max:255', 'unique:users,email'],
                    'company_phone' => ['required', 'string', 'max:50'],
                    'company_type' => ['required', 'string', 'max:255'],
                    'registration_number' => ['required', 'string', 'max:255'],
                    'logo' => ['nullable', 'image', 'max:2048'],
                ];
            } else {
                $rules += [
                    'full_name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                    'phone' => ['required', 'string', 'max:50'],
                    'gender' => ['nullable', Rule::in(['female', 'male', 'other'])],
                    'sponsor_type' => ['nullable', Rule::in(['funding', 'donation'])],
                ];
            }
        } else {
            $rules += [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'string', 'max:50'],
                'date_of_birth' => ['nullable', 'date'],
                'gender' => ['nullable', Rule::in(['female', 'male', 'other'])],
            ];
        }

        return $rules;
    }
}
