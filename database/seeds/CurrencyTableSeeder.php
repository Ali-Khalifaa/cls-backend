<?php

use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Currency::create([
           'name' => 'EGP'
        ]);

        \App\Models\Currency::create([
           'name' => ' USD'
        ]);

        \App\Models\Currency::create([
           'name' => ' EUR'
        ]);
    }
}
