<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    // List invoices for the authenticated patient
    public function index()
    {
        $invoices = Invoice::whereHas('application', function ($query) {
            $query->where('patient_id', auth()->user()->patient->id);
        })->with('application')->get();

        return view('patient.invoices', compact('invoices'));
    }

    // Show a single invoice with details
    public function show(Invoice $invoice)
    {
        // Optional: verify the user owns the invoice
        abort_unless($invoice->application->patient_id === auth()->user()->patient->id, 403);
        return view('patient.show-invoice', compact('invoice'));
    }

    // Download the PDF if available
    public function download(Invoice $invoice)
    {
        abort_unless($invoice->application->patient_id === auth()->user()->patient->id, 403);

        return $invoice->file_path
            ? Storage::download($invoice->file_path, $invoice->invoice_number . '.pdf')
            : abort(404, 'No file attached to this invoice.');
    }

    // Admin store method (not shown here) would validate and save invoices
}
