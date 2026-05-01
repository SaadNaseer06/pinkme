<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('messages', 'messages_sender_receiver_sent_at_idx', function (Blueprint $table): void {
            $table->index(['sender_id', 'receiver_id', 'sent_at'], 'messages_sender_receiver_sent_at_idx');
        });

        $this->addIndexIfMissing('messages', 'messages_receiver_read_sender_idx', function (Blueprint $table): void {
            $table->index(['receiver_id', 'is_read', 'sender_id'], 'messages_receiver_read_sender_idx');
        });

        $this->addIndexIfMissing('program_registrations', 'program_regs_status_assigned_idx', function (Blueprint $table): void {
            $table->index(['status', 'assigned_case_manager_id'], 'program_regs_status_assigned_idx');
        });

        $this->addIndexIfMissing('program_registrations', 'program_regs_status_finance_sent_idx', function (Blueprint $table): void {
            $table->index(['status', 'finance_user_id', 'sent_to_finance_at'], 'program_regs_status_finance_sent_idx');
        });

        $this->addIndexIfMissing('applications', 'applications_reviewer_status_created_idx', function (Blueprint $table): void {
            $table->index(['reviewer_id', 'status', 'created_at'], 'applications_reviewer_status_created_idx');
        });
    }

    public function down(): void
    {
        $this->dropIndexIfExists('messages', 'messages_sender_receiver_sent_at_idx');
        $this->dropIndexIfExists('messages', 'messages_receiver_read_sender_idx');
        $this->dropIndexIfExists('program_registrations', 'program_regs_status_assigned_idx');
        $this->dropIndexIfExists('program_registrations', 'program_regs_status_finance_sent_idx');
        $this->dropIndexIfExists('applications', 'applications_reviewer_status_created_idx');
    }

    private function addIndexIfMissing(string $table, string $indexName, callable $callback): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($callback): void {
            $callback($blueprint);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName): void {
            $blueprint->dropIndex($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
