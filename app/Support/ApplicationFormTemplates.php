<?php

namespace App\Support;

use Illuminate\Support\Str;

final class ApplicationFormTemplates
{
    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public static function all(): array
    {
        return [
            ProgramType::MAMMOGRAM_IMAGING => self::mammogramImaging(),
            ProgramType::FOOD_ASSISTANCE => self::foodAssistance(),
            ProgramType::FINANCIAL_ASSISTANCE => self::basicApplicant(),
        ];
    }

    public static function forType(?string $programType): array
    {
        return self::all()[$programType ?? ''] ?? [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function mammogramImaging(): array
    {
        return [
            self::section('Overview', 'Program overview'),
            self::guidelines('overview', 'PINK "ME" partners with local diagnostic centers to provide financial support for 3D mammogram screenings, follow-up diagnostics, and survivor wellness imaging.'),
            self::section('Applicant Information'),
            self::field('first_name', 'First Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'first_name'),
            self::field('last_name', 'Last Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'last_name'),
            self::field('dob', 'Date of Birth', ApplicationFormFieldTypes::DATE, required: true, mapsTo: 'dob'),
            self::field('phone', 'Phone Number', ApplicationFormFieldTypes::PHONE, required: true, mapsTo: 'phone'),
            self::field('email', 'Email Address', ApplicationFormFieldTypes::EMAIL, required: true, mapsTo: 'email'),
            self::field('contact_preference', 'Preferred Method of Contact', ApplicationFormFieldTypes::SELECT, required: true, options: ['Phone', 'Email', 'Text']),
            self::field('street_address', 'Street Address', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'street_address'),
            self::field('city', 'City', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'city'),
            self::field('state', 'State', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'state'),
            self::field('postal_code', 'ZIP Code', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'postal_code'),
            self::section('Program Request'),
            self::field('type_of_support', 'Type of Support', ApplicationFormFieldTypes::SELECT, required: true, options: [
                '3D Mammogram',
                'Follow-Up Diagnostic Mammogram',
                'Breast Ultrasound',
                'Breast MRI',
                'Survivor Health & Wellness Screening',
                'Other',
            ]),
            self::field('breast_health_concerns', 'Current Breast Health Concerns', ApplicationFormFieldTypes::RADIO, required: true, options: ['Yes', 'No', 'Prefer not to answer']),
            self::field('prior_abnormal_results', 'Prior Abnormal Screening Results', ApplicationFormFieldTypes::RADIO, required: true, options: ['Yes', 'No', 'Not applicable']),
            self::field('last_screening_date', 'Date of Last Screening', ApplicationFormFieldTypes::DATE),
            self::section('Survivor Health & Wellness Support'),
            self::field('is_survivor', 'Are you a breast cancer survivor requesting support?', ApplicationFormFieldTypes::RADIO, required: true, options: ['Yes', 'No']),
            self::field('survivor_support_type', 'Type of Support Needed', ApplicationFormFieldTypes::SELECT, options: [
                'Annual Mammogram',
                'Breast MRI',
                'Breast Ultrasound',
                'Diagnostic Mammogram',
                'Follow-Up Imaging After Abnormal Result',
                'Other',
            ], conditionalField: 'is_survivor', conditionalValue: 'Yes'),
            self::section('Financial Assistance Information'),
            self::field('requesting_financial_support', 'Requesting financial support due to cost?', ApplicationFormFieldTypes::RADIO, required: true, options: ['Yes', 'No']),
            self::field('health_insurance', 'Current Health Insurance', ApplicationFormFieldTypes::SELECT, required: true, options: [
                'Yes',
                'No',
                'Medicaid',
                'Medicare',
                'Marketplace/Private Insurance',
                'Unsure',
            ]),
            self::field('explanation_of_need', 'Explanation of Need', ApplicationFormFieldTypes::LONG_TEXT, required: true, mapsTo: 'story'),
            self::section('X-Ray Associates of New Mexico'),
            self::field('preferred_center', 'Preferred Participating Center', ApplicationFormFieldTypes::SELECT, required: true, options: [
                'X-Ray Associates of New Mexico',
                'No preference',
                'Assistance needed',
            ]),
            self::field(
                'permission_to_share',
                'Permission to share information with X-Ray Associates of New Mexico',
                ApplicationFormFieldTypes::CHECKBOX,
                required: true,
                helpText: 'I authorize PINK "ME" to share my information with X-Ray Associates of New Mexico for imaging support.'
            ),
            self::guidelines(
                'xray_scheduling_note',
                'Note: Once your application has been approved by PINK "ME", X-Ray Associates of New Mexico will coordinate the scheduling of the appointment and provide the patient with the imaging location.'
            ),
            self::section('Required Acknowledgments'),
            self::field('ack_no_guarantee', 'I understand that submitting this application does not guarantee approval.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('ack_center_contact', 'I understand that approved applications will be submitted to X-Ray Associates of New Mexico for scheduling.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('ack_billed_to_pinkme', 'I understand that approved services will be billed directly to PINK "ME".', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('ack_no_medical_advice', 'I understand that PINK "ME" does not provide medical advice or treatment.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('ack_accurate_info', 'I certify that all information provided is accurate to the best of my knowledge.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::section('Final Submission'),
            self::field('signature_name', 'Full Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true),
            self::field('signature_date', 'Date', ApplicationFormFieldTypes::DATE, required: true),
            self::field('signature', 'Signature', ApplicationFormFieldTypes::SIGNATURE, required: true),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function foodAssistance(): array
    {
        return [
            self::section('Program Guidelines', 'Program guidelines'),
            self::guidelines('guidelines', 'The PINK "ME" Up Food Support Program provides limited grocery assistance to breast cancer patients and survivors. Eligible applicants may receive a $100 Electronic Food Card. Maximum of three awards per lifetime.'),
            self::section('Applicant Information'),
            self::field('first_name', 'First Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'first_name'),
            self::field('last_name', 'Last Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'last_name'),
            self::field('dob', 'Date of Birth', ApplicationFormFieldTypes::DATE, required: true, mapsTo: 'dob'),
            self::field('phone', 'Phone Number', ApplicationFormFieldTypes::PHONE, required: true, mapsTo: 'phone'),
            self::field('email', 'Email Address', ApplicationFormFieldTypes::EMAIL, required: true, mapsTo: 'email'),
            self::field('contact_preference', 'Preferred Method of Contact', ApplicationFormFieldTypes::SELECT, required: true, options: ['Phone', 'Email', 'Text Message']),
            self::field('street_address', 'Street Address', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'street_address'),
            self::field('apartment_suite', 'Apartment/Unit Number', ApplicationFormFieldTypes::SHORT_TEXT, mapsTo: 'apartment_suite'),
            self::field('city', 'City', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'city'),
            self::field('state', 'State', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'state'),
            self::field('postal_code', 'ZIP Code', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'postal_code'),
            self::section('Patient Information'),
            self::field('patient_status', 'Current status', ApplicationFormFieldTypes::SELECT, required: true, options: [
                'Currently Receiving Breast Cancer Treatment',
                'Newly Diagnosed Breast Cancer Patient',
                'Breast Cancer Survivor',
                'Metastatic Breast Cancer Patient',
            ]),
            self::field('diagnosis_date', 'Date of Breast Cancer Diagnosis', ApplicationFormFieldTypes::DATE, required: true),
            self::field('oncology_provider', 'Name of Oncology Provider', ApplicationFormFieldTypes::SHORT_TEXT, required: true),
            self::field('oncology_practice', 'Oncology Practice, Hospital, or Cancer Center', ApplicationFormFieldTypes::SHORT_TEXT, required: true),
            self::field('active_treatment', 'Are you currently receiving active treatment?', ApplicationFormFieldTypes::RADIO, required: true, options: ['Yes', 'No']),
            self::field('treatment_types', 'If receiving treatment, select all that apply', ApplicationFormFieldTypes::CHECKBOX_GROUP, options: [
                'Chemotherapy',
                'Radiation Therapy',
                'Hormone Therapy',
                'Immunotherapy',
                'Targeted Therapy',
                'Surgery Scheduled',
                'Reconstruction',
                'Other',
            ], conditionalField: 'active_treatment', conditionalValue: 'Yes'),
            self::field('survivor_duration', 'If survivor, how long since treatment completed?', ApplicationFormFieldTypes::SELECT, options: [
                'Less than 1 Year',
                '1-3 Years',
                '4-5 Years',
                'More than 5 Years',
            ]),
            self::section('Household Information'),
            self::field('employment_status', 'Current Employment Status', ApplicationFormFieldTypes::SELECT, required: true, options: [
                'Full-Time',
                'Part-Time',
                'Self-Employed',
                'Not Currently Employed',
                'Retired',
                'Disabled',
            ]),
            self::section('Food Support Request'),
            self::field('financial_hardship', 'Requesting grocery assistance due to financial hardship?', ApplicationFormFieldTypes::RADIO, required: true, options: ['Yes', 'No']),
            self::field('need_description', 'Describe your current need for food assistance', ApplicationFormFieldTypes::LONG_TEXT, required: true, mapsTo: 'story'),
            self::field('preferred_grocery', 'Preferred Electronic Grocery Card', ApplicationFormFieldTypes::SELECT, required: true, options: [
                'Aldi',
                "Smith's/Kroger",
                'Food Lion',
                'Albertsons',
                'Publix',
                'Whole Foods',
                'Trader Joe\'s',
                'Natural Groceries',
                'Safeway',
                'Other',
            ]),
            self::section('Lifetime Assistance Eligibility'),
            self::field('prior_food_cards', 'Have you previously received a PINK "ME" Electronic Food Card?', ApplicationFormFieldTypes::SELECT, required: true, options: [
                'No, this is my first request',
                'Yes, I have received one (1) food card',
                'Yes, I have received two (2) food cards',
                'Yes, I have received three (3) food cards',
            ]),
            self::section('Required Documentation'),
            self::field('breast_cancer_verification', 'Upload Breast Cancer Verification Documentation', ApplicationFormFieldTypes::FILE, required: true),
            self::field('treatment_verification', 'Upload Treatment or Survivor Verification', ApplicationFormFieldTypes::FILE, required: true),
            self::section('Supporting Grocery Documentation'),
            self::field(
                'upload_supporting_docs',
                'I need to upload supporting grocery documentation',
                ApplicationFormFieldTypes::CHECKBOX,
                helpText: 'Check this box if you are applying for a 2nd or 3rd food card award, or otherwise need to attach grocery documentation. (Final wording TBD.)'
            ),
            self::field(
                'previous_receipt',
                'Upload Previous Grocery Receipt / Supporting Documentation',
                ApplicationFormFieldTypes::FILE,
                required: true,
                conditionalField: 'upload_supporting_docs',
                conditionalValue: '1'
            ),
            self::section('Certifications'),
            self::field('cert_diagnosis', 'I certify that I have been diagnosed with breast cancer.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('cert_accurate', 'I certify that all information provided is true and accurate.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('cert_no_guarantee', 'I understand that submission does not guarantee approval.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::field('cert_max_three', 'I understand I may receive a maximum of three Electronic Food Card awards.', ApplicationFormFieldTypes::CHECKBOX, required: true),
            self::section('Electronic Signature'),
            self::field('signature_name', 'Full Legal Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true),
            self::field('signature_date', 'Date', ApplicationFormFieldTypes::DATE, required: true),
            self::field('signature', 'Electronic Signature', ApplicationFormFieldTypes::SIGNATURE, required: true),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function basicApplicant(): array
    {
        return [
            self::section('Applicant Information'),
            self::field('first_name', 'First Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'first_name'),
            self::field('last_name', 'Last Name', ApplicationFormFieldTypes::SHORT_TEXT, required: true, mapsTo: 'last_name'),
            self::field('email', 'Email Address', ApplicationFormFieldTypes::EMAIL, required: true, mapsTo: 'email'),
            self::field('phone', 'Phone Number', ApplicationFormFieldTypes::PHONE, required: true, mapsTo: 'phone'),
            self::field('dob', 'Date of Birth', ApplicationFormFieldTypes::DATE, required: true, mapsTo: 'dob'),
            self::field('story', 'Tell us about your need', ApplicationFormFieldTypes::LONG_TEXT, required: true, mapsTo: 'story'),
            self::field('signature', 'Signature', ApplicationFormFieldTypes::SIGNATURE, required: true),
        ];
    }

    /**
     * @param  list<string>  $options
     * @return array<string, mixed>
     */
    private static function field(
        string $name,
        string $label,
        string $type,
        bool $required = false,
        ?string $mapsTo = null,
        array $options = [],
        ?string $conditionalField = null,
        ?string $conditionalValue = null,
        ?string $helpText = null,
        ?string $section = null,
    ): array {
        $field = [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'label' => $label,
            'type' => $type,
            'section' => $section ?? '',
            'required' => $required,
            'help_text' => $helpText ?? '',
            'options' => ApplicationFormSchema::normalizeOptions($options),
            'maps_to_column' => $mapsTo,
            'conditional' => null,
        ];

        if ($conditionalField && $conditionalValue) {
            $field['conditional'] = [
                'field' => $conditionalField,
                'value' => $conditionalValue,
            ];
        }

        return $field;
    }

    private static function section(string $title, ?string $name = null): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $name ?? ApplicationFormSchema::slugifyName($title),
            'label' => $title,
            'type' => ApplicationFormFieldTypes::SECTION_HEADER,
            'section' => $title,
            'required' => false,
            'help_text' => '',
            'options' => [],
            'maps_to_column' => null,
            'conditional' => null,
        ];
    }

    private static function guidelines(string $name, string $text): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'label' => $text,
            'type' => ApplicationFormFieldTypes::GUIDELINES,
            'section' => '',
            'required' => false,
            'help_text' => '',
            'options' => [],
            'maps_to_column' => null,
            'conditional' => null,
        ];
    }
}
