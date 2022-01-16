<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientRole = config('roles.models.role')::where('name', '=', 'Client')->first();
        $employeeRole = config('roles.models.role')::where('name', '=', 'Employee')->first();
        $permissions = config('roles.models.permission')::all();

        /*
         * Add Users
         *
         */
        if (config('roles.models.defaultUser')::where('email', '=', 'admin@admin.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'name'     => 'Gustavo',
                'surname'  => 'Escobar',
                'email'    => 'gustavo.escobar@employee.com',
                'password' => bcrypt('password'),
            ]);

            $newUser->attachRole($employeeRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'admin@admin.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'name'     => 'Florimar',
                'surname'  => 'Tolosa',
                'email'    => 'florimar.tolosa@employee.com',
                'password' => bcrypt('password'),
            ]);

            $newUser->attachRole($employeeRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'user@user.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'name'     => 'Pedro',
                'surname'  => 'Parque',
                'email'    => 'pedro.parque@user.com',
                'password' => bcrypt('password'),
            ]);

            $newUser->attachRole($clientRole);
        }
    }
}
