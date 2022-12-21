<?php

use Illuminate\Database\Seeder;

class MaterialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Material::create([
            'name'=>'Official'
        ]);
        \App\Models\Material::create([
            'name'=>'Soft Copy CD'
        ]);
        \App\Models\Material::create([
            'name'=>'Soft Copy Flash Memory'
        ]);
        \App\Models\Material::create([
            'name'=>'Hard Copy'
        ]);
    }
}
