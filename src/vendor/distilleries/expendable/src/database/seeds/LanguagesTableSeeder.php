<?php

use Distilleries\Expendable\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder {

    public function run()
    {
        Language::create([
            'label'       => 'English',
            'iso'         => 'en_US',
            'status'      => true,
            'not_visible' => false,
            'is_default'  => true,
        ]);

        Language::create([
            'label'       => 'French',
            'iso'         => 'fr_CA',
            'status'      => true,
            'not_visible' => false,
            'is_default'  => false,
        ]);

        //Language::create([
        //    'label'       => 'Español',
        //    'iso'         => 'es_MX',
        //    'status'      => true,
        //    'not_visible' => false,
        //    'is_default'  => false,
        //]);

    }
}