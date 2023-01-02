<?php

use Database\Seeders\LaratrustSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LaratrustSeeder::class);
//        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(FollowupSeeder::class);
        $this->call(CompanyFollowupSeeder::class);
        $this->call(QuestionTypeSeeder::class);
        $this->call(DaysTableSeeder::class);
        $this->call(MonthTableSeeder::class);
        $this->call(LeadSourceSeeder::class);
        $this->call(CateringSeeder::class);
        $this->call(MilitaryTableSeeder::class);
        $this->call(CommissionTypeTableSeeder::class);
        $this->call(PerTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(AvailabilityTableSeeder::class);
        $this->call(InstructorPerTableSeeder::class);
        $this->call(SubjectTableSeeder::class);
        $this->call(MaterialTableSeeder::class);

    }//end of run
}//end of seeder
