<?php

use App\Enums\BookApprovalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {

            if (!Schema::hasColumn('books', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('books', 'approval_status')) {
                $table->unsignedTinyInteger('approval_status')
                    ->default(BookApprovalStatusEnum::PENDING->value)
                    ->comment('1=approved, 2=pending, 3=rejected')
                    ->index();
            }

            if (!Schema::hasColumn('books', 'approved_by')) {
                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('books', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }

            if (!Schema::hasColumn('books', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }

            if (!Schema::hasColumn('books', 'rejection_reason')) {
                $table->string('rejection_reason', 500)->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {

            if (Schema::hasColumn('books', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('books', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            if (Schema::hasColumn('books', 'approval_status')) {
                $table->dropColumn('approval_status');
            }

            if (Schema::hasColumn('books', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('books', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }

            if (Schema::hasColumn('books', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }

        });
    }
};