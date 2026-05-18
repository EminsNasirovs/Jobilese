<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_details', function (Blueprint $table) {
            if (!Schema::hasColumn('cv_details', 'title')) {
                $table->string('title', 120)->default('Mans CV')->after('user_id');
            }
            if (!Schema::hasColumn('cv_details', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('title');
            }
            if (!Schema::hasColumn('cv_details', 'template')) {
                $table->string('template', 32)->default('editorial')->after('skills');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cv_details', function (Blueprint $table) {
            $table->dropColumn(array_filter(
                ['title', 'is_default', 'template'],
                fn($col) => Schema::hasColumn('cv_details', $col)
            ));
        });
    }
};
