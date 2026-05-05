<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Token (disimpan hashed di DB)
            $table->string('switch_token', 100)->nullable()->after('remember_token');
            // Expired dalam 30 hari sejak terakhir login
            $table->timestamp('switch_token_expires_at')->nullable()->after('switch_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['switch_token', 'switch_token_expires_at']);
        });
    }
};
