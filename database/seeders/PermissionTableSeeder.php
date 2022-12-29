<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissionsAdmin = config('acl.super_admin_permission');
        $permissionsOperation = config('acl.operation_permission');
        $permissionsProvider = config('acl.provider_permission');

        $permissions=array_merge($permissionsAdmin,$permissionsProvider,$permissionsOperation);

        $allPermissions = [];
        foreach ($permissions as $per) {
            foreach ($per as $item) {
                $allPermissions[] = $item;
            }
        }
        //list all $permissions
        foreach ($allPermissions as $permission)
         {
            Permission::create(['guard_name'=>'api','name' => $permission]);
         }

        }
}
