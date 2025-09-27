<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

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
        $settings  = SiteSetting::first() ?? new SiteSetting();
        $validated = []; 

        switch ($tab) {
            case 'general':
                $validated = $request->validate([
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
                ]);
                break;

            case 'privacy':
                $validated = $request->validate([
                    'privacy_policy_content' => 'nullable|string',
                    'privacy_last_updated'   => 'nullable|date',
                ]);
                break;

            case 'terms':
                $validated = $request->validate([
                    'terms_conditions_content' => 'nullable|string',
                    'terms_last_updated'       => 'nullable|date',
                ]);
                break;

            default:
                return back()->with('error', 'Invalid settings tab.');
        }

        $settings->fill($validated)->save();

        return back()->with('success', ucfirst($tab) . ' settings updated successfully!');
    }
}
