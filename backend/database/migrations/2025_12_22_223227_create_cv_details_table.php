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
        Schema::create('cv_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->text('summary')->nullable(); // A "About Me" blurb
        $table->json('experience')->nullable(); // Array of {company, role, years, desc}
        $table->json('education')->nullable();  // Array of {school, degree, year}
        $table->json('skills')->nullable();     // Array of strings ['Vue', 'Laravel']
        $table->string('template', 32)->default('editorial'); // Layout: editorial / sidebar / minimal
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_details');
    }
};
