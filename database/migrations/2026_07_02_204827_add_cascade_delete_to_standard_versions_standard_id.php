<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standard_versions', function (Blueprint $table) {
            $table->dropForeign(['standard_id']);
            $table->foreign('standard_id')->references('id')->on('standards')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('standard_versions', function (Blueprint $table) {
            $table->dropForeign(['standard_id']);
            $table->foreign('standard_id')->references('id')->on('standards');
        });
    }
};
