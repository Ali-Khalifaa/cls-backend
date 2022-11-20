<?php

use Illuminate\Database\Seeder;

class CateringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Catering::create([
            'name'=>'Coffee break'
        ]);

        \App\Models\Catering::create([
            'name'=>'Coffee break and snakes'
        ]);

        \App\Models\Catering::create([
            'name'=>'Lunch break'
        ]);

        \App\Models\Catering::create([
            'name'=>'Coffee break and snakes and Lunch break'
        ]);
    }
}
