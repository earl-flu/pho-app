<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('pr_categories')->insert([
            [
                'name' => 'ICT Equipment',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        DB::table('pr_items')->insert([
            [
                'category_id' => 1,
                'name' => 'Desktop Computer',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        DB::table('pr_item_details')->insert([
            [
                'item_id' => 1,
                'description' => 'i7, DDR4 16GB RAM, 24" Monitor 100Hz, 1TB SSD',
                'uom' => 'piece',
                'website_link' => 'https://google.com',
                'original_price' => 50000.00,
                'markup_price' => 65000.00,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
