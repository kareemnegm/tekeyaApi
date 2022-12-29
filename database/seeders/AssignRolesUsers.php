<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Provider;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignRolesUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers=Provider::get();

        if($providers){
            foreach($providers as $provider){
                $roleProvider = Role::where('name' ,'provider')->first();
                $provider->assignRole($roleProvider);
            }
        }


        $admins=Admin::get();

        if($admins){
            foreach($admins as $admin){
                $roleAdmin = Role::where('name','super_admin')->first();
                $admin->assignRole($roleAdmin);
            }
        }
    }
}
