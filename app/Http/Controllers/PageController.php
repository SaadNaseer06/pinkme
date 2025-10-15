<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Support\Carbon;

class PageController extends Controller
{
    public function privacy()
    {
        $settings = SiteSetting::first();
        $content = $settings?->privacy_policy_content;
        $lastUpdated = $settings?->privacy_last_updated;

        if (empty(trim((string) $content))) {
            $content = <<<'HTML'
<h3>Privacy Policy</h3>
<p>We value your privacy. This policy explains how we collect, use, and protect your personal information when using our services.</p>
<ul>
    <li>We do not share your data with third parties without your consent.</li>
    <li>We use cookies to improve your experience.</li>
    <li>You can contact us anytime to update or delete your data.</li>
</ul>
HTML;
        }

        return view('pages.policy', [
            'title' => 'Privacy Policy',
            'content' => $content,
            'lastUpdated' => $lastUpdated ? Carbon::parse($lastUpdated) : null,
        ]);
    }

    public function terms()
    {
        $settings = SiteSetting::first();
        $content = $settings?->terms_conditions_content;
        $lastUpdated = $settings?->terms_last_updated;

        if (empty(trim((string) $content))) {
            $content = <<<'HTML'
<h3>Terms &amp; Conditions</h3>
<p>By using our website and services, you agree to the following terms and conditions:</p>
<ol>
    <li>You must be at least 18 years old to use our services.</li>
    <li>Do not misuse or attempt to hack our system.</li>
    <li>We reserve the right to update these terms at any time.</li>
</ol>
HTML;
        }

        return view('pages.policy', [
            'title' => 'Terms & Conditions',
            'content' => $content,
            'lastUpdated' => $lastUpdated ? Carbon::parse($lastUpdated) : null,
        ]);
    }
}
