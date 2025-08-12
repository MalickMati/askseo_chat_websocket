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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->date('date');

            // Default status is 'late' — to be updated to 'present' or 'absent' later
            $table->string('status')->default('late');

            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('checkout_method')->nullable();

            $table->timestamps();

            // Prevent duplicate entries for same user/date
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
