<?php

use Illuminate\Database\Seeder;

class CommissionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\CommissionType::create([
           'title' => 'Individual'
        ]);

        \App\Models\CommissionType::create([
           'title' => 'Corporate'
        ]);

        \App\Models\CommissionType::create([
           'title' => 'Project'
        ]);
    }
}
