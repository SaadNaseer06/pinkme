<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50', 'unique:user_profiles,phone'],
            'password' => ['required', Password::defaults()],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['female', 'male', 'other'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $patientRoleId = Role::where('name', 'patient')->value('id') ?? 2;

        $user = DB::transaction(function () use ($validated, $patientRoleId) {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $patientRoleId,
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'date_of_birth' => $this->normalizeDate($validated['date_of_birth'] ?? null),
                'gender' => $validated['gender'] ?? null,
            ]);

            return $user;
        });

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUser($user->fresh(['profile', 'role'])),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $validator->validated();
        $user = User::query()
            ->where('email', $credentials['login'])
            ->orWhereHas('profile', function ($query) use ($credentials) {
                $query->where('phone', $credentials['login']);
            })
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid login or password.',
            ], 422);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUser($user->loadMissing(['profile', 'role'])),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->formatUser(
                $request->user()->loadMissing(['profile', 'role'])
            ),
        ]);
    }

    /**
     * Normalize incoming date strings to Y-m-d for DB storage.
     */
    private function normalizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Throwable $e) {
                // try next format
            }
        }

        return $date; // let validation/DB handle invalid formats
    }

    private function formatUser(User $user): array
    {
        $profile = $user->profile;

        return [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name ?? null,
            'full_name' => $profile->full_name ?? null,
            'phone' => $profile->phone ?? null,
            'date_of_birth' => $profile->date_of_birth ?? null,
            'gender' => $profile->gender ?? null,
        ];
    }
}
