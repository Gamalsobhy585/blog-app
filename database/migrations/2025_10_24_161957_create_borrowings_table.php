<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('member_id');
            $table->date('borrowed_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['1', '2', '3'])->comment(
                '1=borrowed, 2=returned, 3=overdue'
            )->default('1');    

            $table->timestamps();


            $table->index(['member_id', 'status']);
            $table->index('due_date');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
