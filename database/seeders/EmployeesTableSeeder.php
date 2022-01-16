<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::create([
            'user_id'           =>      1,
            'date_of_admission' =>      '2021/12/31',
            'is_first_job'      =>      false,
        ]);

        Employee::create([
            'user_id'           =>      2,
            'date_of_admission' =>      '2021/12/31',
            'is_first_job'      =>      true,
        ]);
    }
}
