<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            PeriodSeeder::class,
            StandardSeeder::class,
            SubStandardSeeder::class,
            PositionSeeder::class,
            QualityPeriodSeeder::class,
            UserSeeder::class,
            AuditEvidenceSeeder::class,
            AuditScoreSeeder::class,
        ]);
    }
}
