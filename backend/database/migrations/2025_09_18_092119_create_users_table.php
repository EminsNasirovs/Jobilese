<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('firstname');
        $table->string('lastname');
        $table->string('username')->unique();
        $table->string('password');
        $table->string('gender')->nullable();
        $table->integer('age')->nullable();
        $table->enum('role', ['bezdarbnieks',  'uzņēmējs', 'admin'])->default('bezdarbnieks');
        $table->string('company_name')->nullable(); // 👈 NEW
        $table->string('company_number')->nullable();
        $table->string('company_address')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
