<?php

use Illuminate\Database\Seeder;

class PerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Per::create([
            'month' => 1
        ]);

        \App\Models\Per::create([
            'month' => 3
        ]);

        \App\Models\Per::create([
            'month' => 6
        ]);

        \App\Models\Per::create([
            'month' => 12
        ]);
    }
}
