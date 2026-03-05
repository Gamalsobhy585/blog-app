<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

 public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            if (!Schema::hasColumn('authors', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('authors', 'approval_status')) {
                $table->enum('approval_status', [1,2,3])->default(2)->index(); 
            }

            if (!Schema::hasColumn('authors', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('authors', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }

            if (!Schema::hasColumn('authors', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }

            if (!Schema::hasColumn('authors', 'rejection_reason')) {
                $table->string('rejection_reason', 500)->nullable();
            }

    
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            if (Schema::hasColumn('authors', 'approved_by')) $table->dropConstrainedForeignId('approved_by');
            if (Schema::hasColumn('authors', 'created_by')) $table->dropConstrainedForeignId('created_by');

            if (Schema::hasColumn('authors', 'approval_status')) $table->dropColumn('approval_status');
            if (Schema::hasColumn('authors', 'approved_at')) $table->dropColumn('approved_at');
            if (Schema::hasColumn('authors', 'rejected_at')) $table->dropColumn('rejected_at');
            if (Schema::hasColumn('authors', 'rejection_reason')) $table->dropColumn('rejection_reason');
        });
    }
};
