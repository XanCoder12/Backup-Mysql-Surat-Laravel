<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // Make file_word nullable (since we purge files by setting to null)
            $table->string('file_word')->nullable()->change();

            // Change jenis to string — PostgreSQL does not use ENUM
            $table->string('jenis')->change();
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // Revert file_word to not nullable
            $table->string('file_word')->nullable(false)->change();

            $table->string('jenis')->change();
        });
    }
};