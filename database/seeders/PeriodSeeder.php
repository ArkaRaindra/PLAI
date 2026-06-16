<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => '2020/2025',
                'start_date' => '2020-09-01',
                'end_date' => '2025-08-31',
                'is_active' => false,
            ],
            [
                'name' => 'Periode Akreditasi 2021/2026',
                'start_date' => '2021-09-01',
                'end_date' => '2026-08-31',
                'is_active' => true,
            ],
        ];
        
        foreach ($data as $item) {
            Period::create($item);
        }
    }
}
