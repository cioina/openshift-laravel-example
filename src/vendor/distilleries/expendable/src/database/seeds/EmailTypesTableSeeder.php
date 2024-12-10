<?php

use Distilleries\Expendable\Models\EmailType;
use Illuminate\Database\Seeder;

class EmailTypesTableSeeder extends Seeder {

    public function run()
    {
        for ($i = 1; $i <= 7; $i++) 
        {
            EmailType::create([
            'email_type_name'   => 'Send Mailgun Email' .  $i,
            'email_type_id'     => $i,
           ]);
        }

    }
}