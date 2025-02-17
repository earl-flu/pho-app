<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('budgets')->insert([
            [
                'name' => 'iHomis+ Budget 2024',
                'amount' => 1000000,
                'remarks' => 'This is a test remarks',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
