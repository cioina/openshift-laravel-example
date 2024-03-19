<?php namespace Acioina\UserManagement;

      use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

      class UserManagementRouteServiceProvider extends ServiceProvider
      {
 
          /**
           * Define your route model bindings, pattern filters, etc.
           *
           * @return void
           */
          public function boot()
          {
              parent::boot();
          }

          public function register()
          {
              if ((php_sapi_name() === 'cli' && !defined('INCLUDE_PATH')) || 
                  \Distilleries\Expendable\Helpers\TranslationUtils::isAdminServiceProvader())
              {
                  $this->app->register(\Distilleries\Expendable\ExpendableServiceProvider::class);
                  $this->app->register(\Distilleries\Expendable\ExpendableRouteServiceProvider::class);
                  $this->app->register(\Distilleries\MailerSaver\MailerSaverServiceProvider::class);
                  $this->app->register(\Maatwebsite\Excel\ExcelServiceProvider::class);
              }
          }

      }











