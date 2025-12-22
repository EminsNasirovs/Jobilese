<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Make both columns nullable to avoid SQLite error
            $table->string('email')->nullable()->unique()->after('lastname');
            $table->date('birth_date')->nullable()->after('gender');

            // ✅ Only drop age if it exists
            if (Schema::hasColumn('users', 'age')) {
                $table->dropColumn('age');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->dropColumn(['email', 'birth_date']);
        });
    }
};
