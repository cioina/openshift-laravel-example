<?php

use Distilleries\Expendable\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder {

    public function run()
    {
        Permission::create([
               'role_id'      => 2,
               'service_id'   => 1,
           ]);

        Permission::create([
            'role_id'      => 2,
            'service_id'   => 2,
        ]);

        Permission::create([
            'role_id'      => 2,
            'service_id'   => 3,
        ]);

        Permission::create([
            'role_id'      => 2,
            'service_id'   => 4,
        ]);

    }
}