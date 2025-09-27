<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'invoice_number',
        'issue_date',
        'payment_purpose',
        'amount',
        'payment_method',
        'status',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date', 
    ];

    // Relationship to the application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    // Automatically generate an invoice number on creation
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . strtoupper(\Str::random(8));
        });
    }
}
