<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_documents', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->string('document_number')->nullable();
            $table->integer('version_no');
            $table->date('effective_date')->nullable();
            $table->string('file_path');
            $table->string('status');
            $table->foreignId('organization_unit_id')->nullable()->constrained('organization_units');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_documents');
    }
};
