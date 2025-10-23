<?php

namespace App\Http\Controllers;

use App\Models\SponsorDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminSponsorController extends Controller
{
    public function show(User $sponsor)
    {
        if (($sponsor->role->name ?? null) !== 'sponsor') {
            abort(404);
        }
        $sponsor->load(['profile', 'sponsorDetail']);
        return view('admin.sponsor.show', compact('sponsor'));
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
