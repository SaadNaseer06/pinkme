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

        $downloadName = ($invoice->invoice_number ?: 'invoice') . '.pdf';
        $storedPath   = $invoice->file_path;

        if (!$storedPath) {
            abort(404, 'No file attached to this invoice.');
        }

        // If the file path is already a full URL, just redirect to it
        if (filter_var($storedPath, FILTER_VALIDATE_URL)) {
            return redirect()->away($storedPath);
        }

        // Prefer the public disk because invoices are typically stored under storage/app/public
        $publicPath = ltrim(str_replace('public/', '', $storedPath), '/');
        if (Storage::disk('public')->exists($publicPath)) {
            return Storage::disk('public')->download($publicPath, $downloadName);
        }

        // Fallback to the default disk path
        if (Storage::exists($storedPath)) {
            return Storage::download($storedPath, $downloadName);
        }

        // Fallback to a raw storage path (e.g., storage/app/{path})
        $absoluteStorage = storage_path('app/' . ltrim($storedPath, '/'));
        if (is_file($absoluteStorage)) {
            return response()->download($absoluteStorage, $downloadName);
        }

        // Final fallback for files saved directly to the public directory
        $absolutePublic = public_path($storedPath);
        if (is_file($absolutePublic)) {
            return response()->download($absolutePublic, $downloadName);
        }

        abort(404, 'Invoice file not found on the server.');
    }

    // Admin store method (not shown here) would validate and save invoices
}
