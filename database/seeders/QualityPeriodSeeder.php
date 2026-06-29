<?php

namespace Database\Seeders;

use App\Models\QualityPeriod;
use App\Models\User;
use Illuminate\Database\Seeder;

class QualityPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first();

        if (! $admin) {
            $admin = User::first();
        }

        $data = [
            [
                'code' => 'QP_2021',
                'name' => 'Periode Kualitas 2021/2022',
                'start_date' => '2021-09-01',
                'end_date' => '2022-08-31',
                'status' => 'closed',
                'is_active' => false,
            ],
            [
                'code' => 'QP_2023',
                'name' => 'Periode Kualitas 2023/2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-08-31',
                'status' => 'closed',
                'is_active' => false,
            ],
            [
                'code' => 'QP_2025',
                'name' => 'Periode Kualitas 2025/2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'status' => 'active',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            QualityPeriod::query()->firstOrCreate(
                ['code' => $item['code']],
                array_merge($item, [
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ])
            );
        }
    }
}
