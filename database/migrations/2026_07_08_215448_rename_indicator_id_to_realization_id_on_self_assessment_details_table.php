<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        $hasSelfAssessmentIndex = true;

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $database = DB::getDatabaseName();

            $indicatorFk = DB::selectOne(
                <<<'SQL'
                select CONSTRAINT_NAME as name
                from information_schema.KEY_COLUMN_USAGE
                where TABLE_SCHEMA = ?
                  and TABLE_NAME = 'self_assessment_details'
                  and COLUMN_NAME = 'indicator_id'
                  and REFERENCED_TABLE_NAME is not null
                limit 1
                SQL,
                [$database],
            );

            if ($indicatorFk?->name) {
                DB::statement("alter table `self_assessment_details` drop foreign key `{$indicatorFk->name}`");
            }

            $hasSelfAssessmentIndex = (bool) DB::selectOne(
                <<<'SQL'
                select 1
                from information_schema.STATISTICS
                where TABLE_SCHEMA = ?
                  and TABLE_NAME = 'self_assessment_details'
                  and INDEX_NAME = 'self_assessment_details_self_assessment_id_index'
                limit 1
                SQL,
                [$database],
            );
        }

        Schema::table('self_assessment_details', function (Blueprint $table) use ($hasSelfAssessmentIndex) {
            // MySQL may use the composite unique index to back the FK on self_assessment_id.
            // Ensure a dedicated index exists before dropping the composite unique.
            if (! $hasSelfAssessmentIndex) {
                $table->index('self_assessment_id');
            }

            $table->dropUnique(['self_assessment_id', 'indicator_id']);

            $table->renameColumn('indicator_id', 'realization_id');

            $table->foreign('realization_id')
                ->references('id')
                ->on('realizations')
                ->cascadeOnDelete();

            $table->unique(['realization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $database = DB::getDatabaseName();

            $realizationFk = DB::selectOne(
                <<<'SQL'
                select CONSTRAINT_NAME as name
                from information_schema.KEY_COLUMN_USAGE
                where TABLE_SCHEMA = ?
                  and TABLE_NAME = 'self_assessment_details'
                  and COLUMN_NAME = 'realization_id'
                  and REFERENCED_TABLE_NAME is not null
                limit 1
                SQL,
                [$database],
            );

            if ($realizationFk?->name) {
                DB::statement("alter table `self_assessment_details` drop foreign key `{$realizationFk->name}`");
            }
        }

        Schema::table('self_assessment_details', function (Blueprint $table) {
            $table->dropUnique(['realization_id']);

            $table->renameColumn('realization_id', 'indicator_id');

            $table->foreign('indicator_id')
                ->references('id')
                ->on('indicators')
                ->cascadeOnDelete();

            $table->unique(['self_assessment_id', 'indicator_id']);
        });
    }
};
