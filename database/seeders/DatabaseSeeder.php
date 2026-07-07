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
            PositionSeeder::class,
            UserSeeder::class,
            OrganizationUnitSeeder::class,
            QualityPeriodSeeder::class,
            StandardSourceSeeder::class,
            StandardSeeder::class,
            StandardVersionSeeder::class,
            IndicatorSeeder::class,
            IndicatorMappingSeeder::class,
            IndicatorOwnerSeeder::class,
            UserPositionSeeder::class,
            TargetSeeder::class,
            RealizationSeeder::class,
            SelfAssessmentSeeder::class,
        ]);
    }
}
