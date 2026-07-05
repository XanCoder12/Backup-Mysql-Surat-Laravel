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
        // Change jenis column to string — PostgreSQL does not use MODIFY COLUMN ENUM
        Schema::table('surats', function (Blueprint $table) {
            $table->string('jenis')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('jenis')->change();
        });
    }
};
