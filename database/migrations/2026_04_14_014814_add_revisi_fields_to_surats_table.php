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
        Schema::table('surats', function (Blueprint $table) {
            $table->boolean('status_revisi')->default(false)->after('status');
            $table->integer('revisi_count')->default(0)->after('status_revisi');
            $table->timestamp('revisi_uploaded_at')->nullable()->after('revisi_count');
        });

        // Change status column to string — PostgreSQL does not support MODIFY COLUMN ENUM
        Schema::table('surats', function (Blueprint $table) {
            $table->string('status')->default('proses')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn(['status_revisi', 'revisi_count', 'revisi_uploaded_at']);
        });

        Schema::table('surats', function (Blueprint $table) {
            $table->string('status')->default('proses')->change();
        });
    }
};
