<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: status column is already string type — PostgreSQL uses string for flexible status
        Schema::table('surats', function (Blueprint $table) {
            $table->string('status')->default('proses')->change();
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('status')->default('proses')->change();
        });
    }
};
