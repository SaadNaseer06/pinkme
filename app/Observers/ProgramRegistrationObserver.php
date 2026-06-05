<?php

namespace App\Observers;

use App\Events\CaseManagerPatientChatsInboxUpdated;
use App\Events\FinanceTeamChatsInboxUpdated;
use App\Models\ProgramRegistration;
use App\Support\PatientApplicationNotifications;

class ProgramRegistrationObserver
{
    public function created(ProgramRegistration $registration): void
    {
        if ($registration->user_id !== null) {
            broadcast(new CaseManagerPatientChatsInboxUpdated);
        }
        PatientApplicationNotifications::programRegistrationSubmitted($registration);
        if ($this->isFinanceQueueRow($registration)) {
            broadcast(new FinanceTeamChatsInboxUpdated);
        }
    }

    public function updated(ProgramRegistration $registration): void
    {
        if ($registration->wasChanged(['assigned_case_manager_id', 'status', 'user_id'])) {
            broadcast(new CaseManagerPatientChatsInboxUpdated);
        }
        if ($registration->wasChanged(['finance_user_id', 'status', 'sent_to_finance_at'])) {
            broadcast(new FinanceTeamChatsInboxUpdated);
        }
        if ($registration->wasChanged('assigned_case_manager_id')) {
            $previous = $registration->getOriginal('assigned_case_manager_id');
            if ($previous === null && $registration->assigned_case_manager_id !== null) {
                PatientApplicationNotifications::programRegistrationCaseManagerAssigned($registration);
            }
        }
    }

    private function isFinanceQueueRow(ProgramRegistration $registration): bool
    {
        return strtolower((string) $registration->status) === ProgramRegistration::STATUS_PENDING_FINANCE
            && $registration->sent_to_finance_at !== null;
    }
}
