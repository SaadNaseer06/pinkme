<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Validator;

class SiteSettingController extends Controller
{
    public function edit()
    {
        // fetch the first row (only one site settings row is needed)
        $settings = SiteSetting::first();

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request, string $tab)
    {
        $settings = SiteSetting::first() ?? new SiteSetting();
        $validated = [];
        $rules = [];

        switch ($tab) {
            case 'site':
                $rules = [
                    'site_name'        => 'nullable|string|max:255',
                    'site_description' => 'nullable|string',
                    'contact_email'    => 'nullable|email',
                    'contact_phone'    => 'nullable|string|max:50',
                    'address'          => 'nullable|string|max:255',
                    'facebook_url'     => 'nullable|url',
                    'twitter_url'      => 'nullable|url',
                    'instagram_url'    => 'nullable|url',
                    'linkedin_url'     => 'nullable|url',
                    'about_us_content' => 'nullable|string',
                ];
                break;

            case 'privacy':
                $rules = [
                    'privacy_policy_content' => 'nullable|string',
                    'privacy_last_updated'   => 'nullable|date',
                ];
                break;

            case 'terms':
                $rules = [
                    'terms_conditions_content' => 'nullable|string',
                    'terms_last_updated'       => 'nullable|date',
                ];
                break;

            default:
                return redirect()->route('admin.settings')->with('error', 'Invalid settings tab.');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.settings', ['tab' => $tab])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $settings->fill($validated)->save();

        return redirect()
            ->route('admin.settings', ['tab' => $tab])
            ->with([
                'success' => ucfirst($tab) . ' settings updated successfully!',
                'active_tab' => $tab,
            ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:4096',
        ]);

        $path = $request->file('upload')->store('policy-images', 'public');

        $url = asset('storage/' . ltrim($path, '/'));

        return response()->json([
            // Response formats supported by CKEditor 5 upload adapters
            'uploaded' => true,
            'url'      => $url,
            'fileName' => basename($path),
            'urls'     => [ 'default' => $url ],
        ], 201);
    }
}
