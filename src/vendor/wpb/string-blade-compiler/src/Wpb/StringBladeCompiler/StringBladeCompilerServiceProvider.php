<?php namespace Wpb\StringBladeCompiler;

      use Illuminate\Support\ServiceProvider;
      //HELP: https://packagist.org/packages/wpb/string-blade-compiler https://gist.github.com/TerrePorter/4d2ed616c6ebeb371775347f7378c035

      class StringBladeCompilerServiceProvider extends ServiceProvider 
      {
          /**
           * Register the service provider.
           *
           * @return void
           */
          public function register()
          {
              $this->app->bind('stringview', 'Wpb\StringBladeCompiler\StringView');

              /*
               * This removes the need to add a facade in the config\app
               */
              $this->app->booting(function()
              {
                  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                  $loader->alias('StringView', 'Wpb\StringBladeCompiler\Facades\StringView');
              });
          }

          /**
           * Get the services provided by the provider.
           *
           * @return array
           */
          public function provides()
          {
              return array();
          }

      }
