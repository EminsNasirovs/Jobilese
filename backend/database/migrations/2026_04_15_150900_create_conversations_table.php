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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vacancy_id')->nullable()->constrained('job_vacancies')->onDelete('set null');
            $table->timestamps();
            $table->unique(['employer_id', 'applicant_id', 'vacancy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
