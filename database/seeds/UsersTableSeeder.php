<?php

use Distilleries\Expendable\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        User::create([
              'email'    => $GLOBALS['CIOINA_Config']->get('LaravelUser'),
              'password' => bcrypt($GLOBALS['CIOINA_Config']->get('LaravelPassword')),
              'status'   => true,
              'role_id'  => 1,
          ]);

        User::create([
            'email'    => $GLOBALS['CIOINA_Config']->get('LaravelWebUser'),
            'password' => bcrypt($GLOBALS['CIOINA_Config']->get('LaravelWebPassword')),
            'status'   => true,
            'role_id'  => 2,
        ]);
    }
}