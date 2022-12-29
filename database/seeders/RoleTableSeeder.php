<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $rolesA = ['admin'];
        // foreach ($rolesA as $role) {
        //     Role::create(['guard_name'=>'admin','name' => $role]);
        // }
        $roles = config('acl.roles');
        
        foreach ($roles as $role) {
            Role::create(['guard_name'=>'api','name' => $role]);
        }


    }

}
