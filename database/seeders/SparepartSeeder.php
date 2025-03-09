<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use Illuminate\Database\Seeder;

class SparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Sparepart::create([
            'name' => 'Spark Plug',
            'description' => 'High-performance spark plug',
            'price' => 10.50,
            'stock' => 100,
            'category_id' => 1,
            'supplier_id' => 1,
        ]);
    }
}
