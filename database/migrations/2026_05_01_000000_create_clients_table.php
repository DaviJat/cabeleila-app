<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Personal Information
            $table->string('full_name');
            $table->string('phone')->unique(); // Primary identifier for WhatsApp/OTP login
            $table->string('email')->unique()->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->date('birth_date')->nullable();

            // Structured Address Information
            $table->string('postal_code', 9)->nullable();
            $table->string('street')->nullable();
            $table->string('number', 10)->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 50);

            // WhatsApp Authentication (OTP)
            // Password remains nullable since the authentication flow relies on codes
            $table->string('password')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();

            // Additional Notes and Metadata
            $table->text('notes')->nullable();
            $table->timestamps();

            // Soft Deletes support (adds 'deleted_at' column)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
