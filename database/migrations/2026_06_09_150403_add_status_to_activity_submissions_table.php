<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            // Guardará: 'pending', 'approved' (apto), 'rejected' (no apto)
            $table->string('status')->default('pending')->after('evidence_photo');
        });
    }

    public function down(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};