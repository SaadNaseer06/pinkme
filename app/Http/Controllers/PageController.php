<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
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

    public function adminGuide(): View
    {
        return $this->portalGuideView('admin');
    }

    public function patientGuide(): View
    {
        return $this->portalGuideView('patient');
    }

    public function caseManagerGuide(): View
    {
        return $this->portalGuideView('case_manager');
    }

    public function financeGuide(): View
    {
        return $this->portalGuideView('finance');
    }

    private function portalGuideView(string $role): View
    {
        $guides = [
            'admin' => [
                'layout' => 'admin.layouts.admin',
                'title' => 'Portal Tutorial',
                'portalName' => 'Admin Portal Tutorial',
                'intro' => 'Use this portal to manage incoming requests, assign work, oversee progress, and move approved cases to the next stage.',
                'quickStart' => [
                    'Open the dashboard to monitor current request volumes and recent activity.',
                    'Go to Applications to review new patient submissions and registration records.',
                    'Assign the appropriate Patient Support Coordinator so each request moves into review.',
                    'Track approved registrations and send them to finance when they are ready.',
                ],
                'sections' => [
                    [
                        'title' => 'Main Areas',
                        'items' => [
                            ['label' => 'Dashboard', 'text' => 'View overall activity, recent applications, and operational summaries.'],
                            ['label' => 'Applications', 'text' => 'Review patient requests and registration items waiting for action.'],
                            ['label' => 'Patient Support Coordinators', 'text' => 'Manage coordinator staff and assign work.'],
                            ['label' => 'Finance Users', 'text' => 'Manage finance access for invoice and budget processing.'],
                            ['label' => 'Programs', 'text' => 'Create and manage programs available to patients.'],
                        ],
                    ],
                    [
                        'title' => 'Workflow',
                        'items' => [
                            ['label' => '1', 'text' => 'Review newly submitted requests.'],
                            ['label' => '2', 'text' => 'Assign each request to a Patient Support Coordinator.'],
                            ['label' => '3', 'text' => 'Monitor status updates and outcomes.'],
                            ['label' => '4', 'text' => 'Send approved registrations to finance when budget action is required.'],
                        ],
                    ],
                ],
                'tips' => [
                    'Use the Applications area as the main operational queue.',
                    'Check status updates regularly so requests do not remain unassigned.',
                    'Use the tutorial link anytime a new staff member needs orientation.',
                ],
            ],
            'patient' => [
                'layout' => 'patient.layouts.app',
                'title' => 'Portal Tutorial',
                'portalName' => 'Patient Portal Tutorial',
                'intro' => 'Use this portal to submit requests, upload documents, follow progress, and communicate with the support team.',
                'quickStart' => [
                    'Open Dashboard to see your current activity and latest updates.',
                    'Use Programs & Services to review available opportunities and open programs.',
                    'Use My Application to track requests you have already submitted.',
                    'Open Chat if you need to respond to the support team or ask a question.',
                ],
                'sections' => [
                    [
                        'title' => 'Main Areas',
                        'items' => [
                            ['label' => 'Dashboard', 'text' => 'See a summary of your requests and latest activity.'],
                            ['label' => 'My Application', 'text' => 'View submitted applications and registration progress.'],
                            ['label' => 'Programs & Services', 'text' => 'Browse programs that are currently available.'],
                            ['label' => 'Chat', 'text' => 'Communicate directly with the team when follow-up is needed.'],
                            ['label' => 'Settings', 'text' => 'Update your account and profile information.'],
                        ],
                    ],
                    [
                        'title' => 'Workflow',
                        'items' => [
                            ['label' => '1', 'text' => 'Submit an application or register for a program.'],
                            ['label' => '2', 'text' => 'Upload all requested documents.'],
                            ['label' => '3', 'text' => 'Check status updates from your account.'],
                            ['label' => '4', 'text' => 'Respond quickly if more information is requested.'],
                        ],
                    ],
                ],
                'tips' => [
                    'Make sure your documents are complete before submitting.',
                    'Check notifications and chat messages regularly.',
                    'Use the portal instead of email where possible so your case stays organized.',
                ],
            ],
            'case_manager' => [
                'layout' => 'case_manager.layouts.app',
                'title' => 'Portal Tutorial',
                'portalName' => 'Case Manager Portal Tutorial',
                'intro' => 'Use this portal to review assigned requests, communicate with patients, and record clear review outcomes.',
                'quickStart' => [
                    'Start on Dashboard to see assigned workload and current status distribution.',
                    'Open Program Registrations to review requests assigned to you.',
                    'Use Patient Profiles and Patient Chats for case context and communication.',
                    'Record approvals, rejections, or missing-information requests promptly.',
                ],
                'sections' => [
                    [
                        'title' => 'Main Areas',
                        'items' => [
                            ['label' => 'Dashboard', 'text' => 'View your assigned workload and key review counts.'],
                            ['label' => 'Program Registrations', 'text' => 'Review and process assigned registration records.'],
                            ['label' => 'Patient Profiles', 'text' => 'View patient details related to your assigned cases.'],
                            ['label' => 'Patient Chats', 'text' => 'Communicate with patients when clarification is needed.'],
                            ['label' => 'Settings', 'text' => 'Manage your own account details and preferences.'],
                        ],
                    ],
                    [
                        'title' => 'Workflow',
                        'items' => [
                            ['label' => '1', 'text' => 'Open the assigned case.'],
                            ['label' => '2', 'text' => 'Review all submitted information and documents.'],
                            ['label' => '3', 'text' => 'Approve, reject, or request missing information.'],
                            ['label' => '4', 'text' => 'Keep the patient informed through the portal when follow-up is required.'],
                        ],
                    ],
                ],
                'tips' => [
                    'Review documents before making a decision.',
                    'Give clear reasons when rejecting or requesting updates.',
                    'Use chat for quick clarification instead of leaving cases idle.',
                ],
            ],
            'finance' => [
                'layout' => 'finance.layouts.app',
                'title' => 'Portal Tutorial',
                'portalName' => 'Finance Portal Tutorial',
                'intro' => 'Use this portal to process approved cases that require recording bills paid and invoice creation.',
                'quickStart' => [
                    'Check Dashboard to see requests waiting for finance action.',
                    'Open Patient Requests to review approved registrations assigned to you.',
                    'Confirm the required financial details and supporting documents.',
                    'Create the invoice to complete the finance step of the request.',
                ],
                'sections' => [
                    [
                        'title' => 'Main Areas',
                        'items' => [
                            ['label' => 'Dashboard', 'text' => 'See finance workload and pending records.'],
                            ['label' => 'Patient Requests', 'text' => 'Review approved registrations sent for finance processing.'],
                        ],
                    ],
                    [
                        'title' => 'Workflow',
                        'items' => [
                            ['label' => '1', 'text' => 'Open an approved registration assigned to finance.'],
                            ['label' => '2', 'text' => 'Review the supporting billing documents.'],
                            ['label' => '3', 'text' => 'Create the invoice.'],
                            ['label' => '4', 'text' => 'Complete recording bills paid and confirm the record is finalized.'],
                        ],
                    ],
                ],
                'tips' => [
                    'Only work on requests assigned to your finance queue.',
                    'Check attached bill statements before generating the invoice.',
                    'Use the portal record as the source of truth for final finance actions.',
                ],
            ],
        ];

        abort_unless(isset($guides[$role]), 404);

        return view('pages.portal-guide', $guides[$role]);
    }
}
