<?php

use Distilleries\Expendable\Models\Service;
use Illuminate\Database\Seeder;

class SevicesTableSeeder extends Seeder {

    public function run()
    {
        Service::create([
               'action' => 'Acioina\\UserManagement\\Http\\Controllers\\PostController@getIndex',
           ]);

        Service::create([
            'action' => 'Acioina\\UserManagement\\Http\\Controllers\\PostController@getIndexDatatable',
        ]);

        Service::create([
            'action' => 'Acioina\\UserManagement\\Http\\Controllers\\PostController@getDatatable',
        ]);

        Service::create([
             'action' => 'Acioina\\UserManagement\\Http\\Controllers\\LanguageMenuController@getIndex',
        ]);

    }
}