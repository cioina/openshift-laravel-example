<?php

if (php_sapi_name() !== 'cli')
{
    require_once __DIR__ . '/../public/libraries/laravel.inc.acioina.php';
}else{
    $temp = getcwd();
    set_include_path(get_include_path() . PATH_SEPARATOR . $temp . DIRECTORY_SEPARATOR . 'public');
    chdir($temp . DIRECTORY_SEPARATOR . 'public');
    require_once getcwd()  . '/libraries/laravel.inc.acioina.php';
    chdir($temp);
}

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                RolesTableSeeder::class,    
                LanguagesTableSeeder::class,
                UsersTableSeeder::class,
                SevicesTableSeeder::class,
                PermissionsTableSeeder::class,
                EmailTypesTableSeeder::class,
            ]);
    }
}
