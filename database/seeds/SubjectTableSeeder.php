<?php

use Illuminate\Database\Seeder;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Subject::create([
            "name" => "call"
        ]);

        \App\Models\Subject::create([
            "name" => "recall"
        ]);

        \App\Models\Subject::create([
            "name" => "email"
        ]);

        \App\Models\Subject::create([
            "name" => "meeting"
        ]);

        \App\Models\Subject::create([
            "name" => "Conference call"
        ]);
    }
}
