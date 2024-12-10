<?php

use Distilleries\Expendable\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder {

    public function run()
    {
        Role::create([
            'label'              => 'superadmin',
            'initials'           => '@sa',
            'overide_permission' => true,
        ]);

        Role::create([
            'label'                 => 'webaccess',
            'initials'              => 'wa',
            'overide_permission'    => false,
        ]);
    }
}