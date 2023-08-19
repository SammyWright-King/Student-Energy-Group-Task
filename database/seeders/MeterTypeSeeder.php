<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeterType;

class MeterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //assign types to array
        $types = array(
            [
                'name' => "MPAN",
                'description' => "electricity meter unique identifier",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => "MPRN",
                'description' => "gas meter unique identifier",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );

        //insert types to meter_type table
        MeterType::insert($types);
    }
}
