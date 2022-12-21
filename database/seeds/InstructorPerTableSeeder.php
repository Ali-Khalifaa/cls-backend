<?php

use Illuminate\Database\Seeder;

class InstructorPerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\InstructorPer::create([
           'title' => 'Hour'
        ]);

        \App\Models\InstructorPer::create([
           'title' => 'Day'
        ]);

        \App\Models\InstructorPer::create([
           'title' => 'Course Or Bundle '
        ]);
    }
}
