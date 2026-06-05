<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_registration_id',
        'invoice_number',
        'issue_date',
        'payment_purpose',
        'amount',
        'payment_method',
        'status',
        'file_path',
        'payment_proof_path',
        'payment_proof_original_name',
        'payment_proof_uploaded_at',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'payment_proof_uploaded_at' => 'datetime',
    ];

    public function programRegistration()
    {
        return $this->belongsTo(ProgramRegistration::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . strtoupper(\Str::random(8));
        });
    }
}
