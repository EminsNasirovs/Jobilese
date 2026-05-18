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
        $table->string('title', 120)->default('Mans CV');
        $table->boolean('is_default')->default(false);
        $table->text('summary')->nullable();
        $table->json('experience')->nullable();
        $table->json('education')->nullable();
        $table->json('skills')->nullable();
        $table->string('template', 32)->default('editorial');
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
