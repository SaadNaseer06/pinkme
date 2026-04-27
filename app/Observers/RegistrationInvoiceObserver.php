<?php

namespace App\Observers;

use App\Events\FinanceTeamChatsInboxUpdated;
use App\Models\RegistrationInvoice;

class RegistrationInvoiceObserver
{
    public function created(RegistrationInvoice $invoice): void
    {
        broadcast(new FinanceTeamChatsInboxUpdated);
    }

    public function deleted(RegistrationInvoice $invoice): void
    {
        broadcast(new FinanceTeamChatsInboxUpdated);
    }
}
