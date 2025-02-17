<?php

namespace Database\Seeders;

use App\Models\PrSignatory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrSignatorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        PrSignatory::create([
            'requested_by' => 'Hazel A. Palmes, M.D.',
            'requested_by_position' => 'Provincial Health Officer II',
            'cash_availability' => 'Erme T. Tablante',
            'cash_availability_position' => 'Acting Provincial Treasurer',
            'approved_by' => 'Joseph C. Cua',
            'approved_by_position' => 'Provincial Governor',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
