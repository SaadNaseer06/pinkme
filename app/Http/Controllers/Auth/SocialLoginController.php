<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google and log the patient in.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Throwable $exception) {
            report($exception);

            return $this->redirectWithError('Unable to authenticate with Google. Please try again.');
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();

        if (!$googleId || !$email) {
            return $this->redirectWithError('We could not retrieve your Google account details. Please use another login method.');
        }

        $user = User::where('provider_name', 'google')
            ->where('provider_id', $googleId)
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();

            if ($user && optional($user->role)->name !== 'patient') {
                return $this->redirectWithError('This email is already registered with a different account type.');
            }
        }

        $patientRoleId = $this->getPatientRoleId();
        $fullName = $googleUser->getName() ?: $googleUser->getNickname() ?: Str::before($email, '@');
        $providerAvatar = $googleUser->getAvatar();
        if (Str::contains($fullName, ' ')) {
            $firstName = Str::beforeLast($fullName, ' ');
            $lastName = Str::afterLast($fullName, ' ');
        } else {
            $firstName = $fullName;
            $lastName = null;
        }

        DB::beginTransaction();

        try {
            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                    'role_id' => $patientRoleId,
                    'provider_name' => 'google',
                    'provider_id' => $googleId,
                    'provider_avatar' => $providerAvatar,
                ]);

                $user->forceFill(['email_verified_at' => now()])->save();
            } else {
                $user->fill([
                    'provider_name' => 'google',
                    'provider_id' => $googleId,
                    'provider_avatar' => $providerAvatar ?: $user->provider_avatar,
                ]);

                if ($user->role_id !== $patientRoleId) {
                    $user->role_id = $patientRoleId;
                }

                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                if ($user->isDirty()) {
                    $user->save();
                }
            }

            $profile = UserProfile::firstOrNew(['user_id' => $user->id]);

            $profile->fill([
                'full_name' => $fullName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $firstName . ' ' . $lastName,
            ]);

            if (!$profile->exists) {
                $profile->phone = 'N/A';
            } elseif (!$profile->phone) {
                $profile->phone = 'N/A';
            }

            $profile->save();

            Patient::firstOrCreate(['user_id' => $user->id]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            report($exception);

            return $this->redirectWithError('Something went wrong while setting up your account. Please try again.');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('patient.dashboard');
    }

    protected function redirectWithError(string $message): RedirectResponse
    {
        return redirect()
            ->route('register', ['tab' => 'login'])
            ->withErrors(['login' => $message]);
    }

    protected function getPatientRoleId(): int
    {
        return (int) Role::query()
            ->where('name', 'patient')
            ->value('id') ?: 2;
    }
}
