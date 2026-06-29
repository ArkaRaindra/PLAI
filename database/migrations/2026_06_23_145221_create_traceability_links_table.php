<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('traceability_links', function (Blueprint $table) {
            $table->id();
            $table
                ->enum('source_type',
                    [
                        'standard_version',
                        'target',
                        'realization',
                        'evidence',
                        'finding',
                        'corrective_action',
                        'risk',
                        'decision',
                        'indicator',
                    ])
                ->nullable();
            $table->bigInteger('source_id');
            $table->string('target_type');
            $table->bigInteger('target_id');
            $table->enum('relation_type',
                [
                    'supported_by',
                    'caused_by',
                    'resolved_by',
                    'reviewed_in',
                    'improved_by',
                    'related_to',
                    'generated_from',
                ]);
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traceability_links');
    }
};
