<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
            OrganizationUnitSeeder::class,
            QualityPeriodSeeder::class,
            StandardSourceSeeder::class,
            // StandardSeeder::class,
            // StandardVersionSeeder::class,
            // IndicatorSeeder::class,
            // TargetSeeder::class,
            // RealizationSeeder::class,
            // UserPositionSeeder::class,
            // IndicatorOwnerSeeder::class,
            // IndicatorMappingSeeder::class,
            // SelfAssessmentSeeder::class,
            // EvidenceSeeder::class,
            // EvidenceReviewSeeder::class,
        ]);
    }
}
