<?php

namespace App\Http\Controllers;

use App\Models\SponsorDetail;
use App\Models\User;
use App\Models\Role;
use App\Mail\SponsorCredentials;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminSponsorController extends Controller
{
    public function create()
    {
        return view('admin.sponsor.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // profile
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:50'],
            // sponsor detail
            'company_name'        => ['nullable', 'string', 'max:255'],
            'company_email'       => ['nullable', 'email', 'max:255'],
            'company_phone'       => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'company_type'        => ['nullable', 'string', 'max:255'],
        ]);

        $sponsorRoleId = Role::where('name', 'sponsor')->value('id');
        if (!$sponsorRoleId) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['role' => 'Sponsor role is not configured.']);
        }

        $sponsor = null;
        DB::transaction(function () use ($data, $sponsorRoleId, &$sponsor) {
            $sponsor = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $sponsorRoleId,
            ]);

            $profile = $sponsor->profile()->create([
                'first_name' => $data['first_name'] ?? null,
                'last_name'  => $data['last_name'] ?? null,
                'full_name'  => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                'phone'      => $data['phone'] ?? null,
                'status'     => 0,
            ]);

            $hasCompanyData = !empty($data['company_name'])
                || !empty($data['company_email'])
                || !empty($data['company_phone'])
                || !empty($data['registration_number'])
                || !empty($data['company_type']);

            if ($hasCompanyData) {
                $sponsor->sponsorDetail()->create([
                    'company_name'        => $data['company_name'] ?? null,
                    'company_email'       => $data['company_email'] ?? null,
                    'company_phone'       => $data['company_phone'] ?? null,
                    'registration_number' => $data['registration_number'] ?? null,
                    'company_type'        => $data['company_type'] ?? null,
                ]);
            }
        });

        try {
            $loginUrl = route('login');
            Mail::to($sponsor->email)->send(new SponsorCredentials($sponsor, $data['password'], $loginUrl));
        } catch (\Throwable $e) {
            Log::warning('Failed to send sponsor credentials email', [
                'sponsor_id' => $sponsor->id ?? null,
                'email' => $sponsor->email ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.sponsors.show', $sponsor)->with('success', 'Sponsor created successfully.');
    }

    public function show(User $sponsor)
    {
        if (($sponsor->role->name ?? null) !== 'sponsor') {
            abort(404);
        }
        $sponsor->load(['profile', 'sponsorDetail']);
        // Status is driven by payment only: Active if at least one event sponsorship is confirmed and paid
        $sponsorStatus = $sponsor->eventSponsorships()
            ->where('registration_status', 'confirmed')
            ->where('payment_status', 'paid')
            ->exists()
            ? 'Active'
            : 'Inactive';
        return view('admin.sponsor.show', compact('sponsor', 'sponsorStatus'));
    }

    public function edit(User $sponsor)
    {
        if (($sponsor->role->name ?? null) !== 'sponsor') {
            abort(404);
        }
        $sponsor->load(['profile', 'sponsorDetail']);
        return view('admin.sponsor.edit', compact('sponsor'));
    }

    public function update(Request $request, User $sponsor)
    {
        if (($sponsor->role->name ?? null) !== 'sponsor') {
            abort(404);
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($sponsor->id)],
            // profile
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            // sponsor detail
            'company_name'        => ['nullable', 'string', 'max:255'],
            'company_email'       => ['nullable', 'email', 'max:255'],
            'company_phone'       => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'company_type'        => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($sponsor, $data) {
            // Update user
            $sponsor->email = $data['email'];
            $sponsor->save();

            // Update profile
            $profile = $sponsor->profile;
            if ($profile) {
                $profile->first_name = $data['first_name'] ?? $profile->first_name;
                $profile->last_name  = $data['last_name']  ?? $profile->last_name;
                $profile->phone      = $data['phone']      ?? $profile->phone;
                // full_name convenience
                if (($data['first_name'] ?? null) || ($data['last_name'] ?? null)) {
                    $profile->full_name = trim(($data['first_name'] ?? $profile->first_name) . ' ' . ($data['last_name'] ?? $profile->last_name));
                }
                $profile->save();
            }

            // Update sponsor detail
            $detail = $sponsor->sponsorDetail ?: new SponsorDetail(['user_id' => $sponsor->id]);
            $detail->company_name        = $data['company_name']        ?? $detail->company_name;
            $detail->company_email       = $data['company_email']       ?? $detail->company_email;
            $detail->company_phone       = $data['company_phone']       ?? $detail->company_phone;
            $detail->registration_number = $data['registration_number'] ?? $detail->registration_number;
            $detail->company_type        = $data['company_type']        ?? $detail->company_type;
            $detail->save();
        });

        return redirect()->route('admin.sponsors.show', $sponsor)->with('success', 'Sponsor updated successfully.');
    }

    public function destroy(User $sponsor)
    {
        if (($sponsor->role->name ?? null) !== 'sponsor') {
            abort(404);
        }

        try {
            $sponsor->delete();
            return redirect()->route('admin.sponsors')->with('success', 'Sponsor removed successfully.');
        } catch (\Throwable $e) {
            // Fallback: deactivate if FK constraints block delete
            $profile = $sponsor->profile;
            if ($profile) {
                $profile->status = 0;
                $profile->save();
            }
            return redirect()->route('admin.sponsors')->with('success', 'Sponsor deactivated (in use by records).');
        }
    }
}
