<?php

use Illuminate\Database\Seeder;

class AvailabilityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Availability::create([
           'title' => 'online'
        ]);

        \App\Models\Availability::create([
           'title' => 'night'
        ]);

        \App\Models\Availability::create([
           'title' => 'Working hours'
        ]);

        \App\Models\Availability::create([
           'title' => 'Friday and Saturday'
        ]);
    }
}
