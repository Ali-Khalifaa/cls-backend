<?php

use Illuminate\Database\Seeder;

class MilitaryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Military::create([
            'title'=> 'Finished'
        ]);

        \App\Models\Military::create([
            'title'=> 'Exempted'
        ]);

        \App\Models\Military::create([
           'title'=> 'Postponed'
        ]);
    }
}
