<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role=Role::where('name','super_admin')->first();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();




    $permissions = config('acl.super_admin_permission');
      $allPermissions = [];
      foreach ($permissions as $per) {
          foreach ($per as $item) {
              $allPermissions[] = $item;
          }
      }



       foreach($allPermissions as $permission){
          $role->givePermissionTo($permission);
      }
    }

}
