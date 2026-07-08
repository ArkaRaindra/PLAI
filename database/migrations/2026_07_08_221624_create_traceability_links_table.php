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
        Schema::create('traceability_links', function (Blueprint $table) {
            $table->id();
            // Source Entity
            $table->unsignedBigInteger('source_id');
            $table->string('source_type', 100);

            // Target Entity
            $table->unsignedBigInteger('target_id');
            $table->string('target_type', 100);

            // Business Relation
            $table->string('relation_type', 100);

            /*
             * defines
             * measured_by
             * supported_by
             * evaluated_in
             * reviewed_in
             * resolved_by
             * caused_by
             * improved_by
             * mapped_to
             * related_to
             */

            // Snapshot / Additional Information
            $table->json('metadata')->nullable();

            // Actor
            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table
                ->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Business Event Time
            $table->timestamp('performed_at')->nullable();

            $table->timestamps();

            /*
             * |--------------------------------------------------------------------------
             * | Index
             * |--------------------------------------------------------------------------
             */

            $table->index(['source_type', 'source_id'], 'trace_source_index');

            $table->index(['target_type', 'target_id'], 'trace_target_index');

            $table->index('relation_type');

            /*
             * |--------------------------------------------------------------------------
             * | Prevent Duplicate Relations
             * |--------------------------------------------------------------------------
             */

            $table->unique([
                'source_type',
                'source_id',
                'target_type',
                'target_id',
                'relation_type',
            ], 'trace_unique_relation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traceability_links');
    }
};
