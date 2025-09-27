<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Application;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Create a few sample invoices for existing applications
        // (assuming you already have applications in the DB)
        $applications = Application::inRandomOrder()->take(3)->get();
        foreach ($applications as $app) {
            Invoice::create([
                'application_id'  => $app->id,
                'issue_date'      => now()->subDays(rand(1, 30)),
                'payment_purpose' => 'Support for ' . $app->title,
                'amount'          => rand(1000, 5000) / 10, // e.g., 100–500
                'payment_method'  => 'Bank Transfer',
                'status'          => 'Paid',
                'file_path'       => null,
                'notes'           => 'Seed data for testing.',
            ]);
        }
    }
}
