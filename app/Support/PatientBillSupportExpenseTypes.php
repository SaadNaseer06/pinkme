<?php

namespace App\Support;

/**
 * Allowed values for "Type of Support Expenses" on applicant bill line items.
 */
final class PatientBillSupportExpenseTypes
{
    /**
     * @var list<string>
     */
    public const OPTIONS = [
        'Hospital',
        'Clinic',
        'Medical Equipment',
        'Medical Bills',
        'Household essential bills',
        'Co-pay',
        'Utilities',
        'Electric Bills',
        'Water Bills',
        'Food Assistance',
        'Car Payment',
        'Cell phone bills',
        'Rent',
    ];
}
