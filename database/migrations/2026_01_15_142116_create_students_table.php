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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id_number')->unique();
            $table->string('campus');
            $table->string('lname'); // Last name
            $table->string('mname')->nullable(); // Middle name
            $table->string('ext')->nullable(); // Name extension (Jr., Sr., etc.)
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('course');
            $table->string('yearlevel');
            $table->string('section');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
