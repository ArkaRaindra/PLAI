<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->standardVersionsReferencesQualityPeriodes()) {
            Schema::table('standard_versions', function (Blueprint $table) {
                $table->dropForeign(['quality_period_id']);
            });

            Schema::table('standard_versions', function (Blueprint $table) {
                $table->foreign('quality_period_id')->references('id')->on('quality_periods');
            });
        }

        if (Schema::hasColumn('targets', 'quality_periode_id')) {
            Schema::table('targets', function (Blueprint $table) {
                $table->dropForeign(['quality_periode_id']);
                $table->dropColumn('quality_periode_id');
            });

            Schema::table('targets', function (Blueprint $table) {
                $table->foreignId('quality_period_id')->after('indicator_id')->constrained('quality_periods');
            });
        }

        Schema::dropIfExists('quality_periodes');
    }

    public function down(): void
    {
        if (! Schema::hasTable('quality_periodes')) {
            Schema::create('quality_periodes', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if ($this->standardVersionsReferencesQualityPeriods()) {
            Schema::table('standard_versions', function (Blueprint $table) {
                $table->dropForeign(['quality_period_id']);
            });

            Schema::table('standard_versions', function (Blueprint $table) {
                $table->foreign('quality_period_id')->references('id')->on('quality_periodes');
            });
        }

        if (Schema::hasColumn('targets', 'quality_period_id') && ! Schema::hasColumn('targets', 'quality_periode_id')) {
            Schema::table('targets', function (Blueprint $table) {
                $table->dropForeign(['quality_period_id']);
                $table->dropColumn('quality_period_id');
            });

            Schema::table('targets', function (Blueprint $table) {
                $table->foreignId('quality_periode_id')->after('indicator_id')->constrained('quality_periodes');
            });
        }
    }

    private function standardVersionsReferencesQualityPeriodes(): bool
    {
        return $this->foreignKeyReferencesTable('standard_versions', 'quality_period_id', 'quality_periodes');
    }

    private function standardVersionsReferencesQualityPeriods(): bool
    {
        return $this->foreignKeyReferencesTable('standard_versions', 'quality_period_id', 'quality_periods');
    }

    private function foreignKeyReferencesTable(string $table, string $column, string $referencedTable): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $foreignKeys = $connection->select("PRAGMA foreign_key_list({$table})");

            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey->from === $column && $foreignKey->table === $referencedTable) {
                    return true;
                }
            }

            return false;
        }

        $database = $connection->getDatabaseName();

        $foreignKey = $connection->selectOne(
            'SELECT referenced_table_name
             FROM information_schema.key_column_usage
             WHERE table_schema = ?
               AND table_name = ?
               AND column_name = ?
               AND referenced_table_name IS NOT NULL
             LIMIT 1',
            [$database, $table, $column],
        );

        return ($foreignKey->referenced_table_name ?? null) === $referencedTable;
    }
};
