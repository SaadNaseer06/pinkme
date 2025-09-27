<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserProfile;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller responsible for displaying and updating the
 * authenticated patient’s settings. Each section of the
 * settings page (personal info, password, notifications,
 * account, and social) is handled by its own method.
 */
class SettingsController extends Controller
{
    /**
     * Show the settings page for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user    = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        $patient = $user->patient ?? new Patient(['user_id' => $user->id]);

        // Render the settings view located at resources/views/patient/setting.blade.php
        return view('patient.setting', compact('user', 'profile', 'patient'));
    }

    /**
     * Update the personal information for the authenticated user.
     * This includes names, username, contact details, demographics
     * and basic profile photo upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        $patient = $user->patient ?? new Patient(['user_id' => $user->id]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // Optional: delete old image
            if ($profile->avatar && Storage::exists($profile->avatar)) {
                Storage::delete($profile->avatar);
            }

            $path = $file->store('public/avatars');
            $profile->avatar = Storage::url($path);
        }

        $rules = [
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'first_name'     => 'nullable|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'username'       => 'nullable|string|max:255|unique:user_profiles,username,' . ($profile->id ?? 'NULL'),
            'phone'          => 'nullable|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'gender'         => 'nullable|string|max:10',
            'blood_group'    => 'nullable|string|max:5',
            'date_of_birth'  => 'nullable|date',
            'marital_status' => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'state'          => 'nullable|string|max:255',
        ];
        $data = $request->validate($rules);

        // Update user email
        $user->email = $data['email'];
        $user->save();

        // Update or create profile fields
        $profile->first_name    = $data['first_name'] ?? $profile->first_name;
        $profile->last_name     = $data['last_name'] ?? $profile->last_name;
        $profile->username      = $data['username'] ?? $profile->username;
        $profile->full_name     = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $profile->phone         = $data['phone'] ?? $profile->phone;
        $profile->gender        = $data['gender'] ?? $profile->gender;
        $profile->date_of_birth = $data['date_of_birth'] ?? $profile->date_of_birth;
        $profile->country       = $data['country'] ?? $profile->country;
        $profile->city          = $data['city'] ?? $profile->city;
        $profile->state         = $data['state'] ?? $profile->state;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }
        $profile->save();

        // Update patient details
        $patient->blood_group    = $data['blood_group'] ?? $patient->blood_group;
        $patient->marital_status = $data['marital_status'] ?? $patient->marital_status;
        $patient->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change the logged in user’s password. Validates the current
     * password and ensures the new password is confirmed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update email/SMS notification preferences for the user’s profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request)
    {
        $profile = $request->user()->profile ?? new UserProfile(['user_id' => $request->user()->id]);
        $profile->email_notification          = $request->has('email_notification');
        $profile->sms_notification            = $request->has('sms_notification');
        $profile->notify_on_new_notifications = $request->has('notify_on_new_notifications');
        $profile->notify_on_direct_message    = $request->has('notify_on_direct_message');
        $profile->save();

        return back()->with('success', 'Notification preferences updated.');
    }

    /**
     * Update account-level fields such as username and alternate email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAccount(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $data = $request->validate([
            'username'        => 'nullable|string|max:255|unique:user_profiles,username,' . ($profile->id ?? 'NULL'),
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'alternate_email' => 'nullable|email',
        ]);

        $user->email = $data['email'];
        $user->save();

        $profile->username        = $data['username'] ?? $profile->username;
        $profile->alternate_email = $data['alternate_email'] ?? $profile->alternate_email;
        $profile->save();

        return back()->with('success', 'Account settings updated successfully.');
    }

    /**
     * Update social media links for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSocial(Request $request)
    {
        $profile = $request->user()->profile ?? new UserProfile(['user_id' => $request->user()->id]);
        $data = $request->validate([
            'facebook'  => 'nullable|url',
            'twitter'   => 'nullable|url',
            'instagram' => 'nullable|url',
        ]);

        $profile->facebook  = $data['facebook'] ?? $profile->facebook;
        $profile->twitter   = $data['twitter'] ?? $profile->twitter;
        $profile->instagram = $data['instagram'] ?? $profile->instagram;
        $profile->save();

        return back()->with('success', 'Social media links updated.');
    }
}
