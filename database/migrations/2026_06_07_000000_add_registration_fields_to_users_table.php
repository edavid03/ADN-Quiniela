<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cedula', 12)->nullable()->unique()->after('username');
            $table->timestamp('approved_at')->nullable()->after('is_admin');
        });

        DB::table('users')->update(['approved_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cedula']);
            $table->dropColumn(['cedula', 'approved_at']);
        });
    }
};
