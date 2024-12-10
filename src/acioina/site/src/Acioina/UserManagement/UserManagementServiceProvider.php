<?php namespace Acioina\UserManagement;
      
      use Acioina\UserManagement\Layouts\LayoutManager;
      use Acioina\UserManagement\States\StateDisplayer;
      
      use Illuminate\Support\ServiceProvider;
      use Illuminate\Contracts\Support\DeferrableProvider;

      class UserManagementServiceProvider extends ServiceProvider implements DeferrableProvider
      {
          protected $router;
          /**
           * This namespace is applied to your controller routes.
           *
           * In addition, it is set as the URL generator's root namespace.
           *
           * @var string
           */
          protected $namespace = 'Acioina\UserManagement\Http\Controllers';

          /**
           * Bootstrap the application events.
           *
           * @return void
           */
          public function boot()
          {
              if (! $this->app->environment(['local','testing'])) 
              {
                  \URL::forceScheme('https');
              }

              $this->loadViewsFrom(__DIR__ . '/../../views', 'user-management');
              $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'user-management');
              $this->publishes([
                  __DIR__ . '/../../config/config.php'    => config_path('user-management.php'),
              ]);
              $this->publishes([
                  __DIR__ . '/../../views' => base_path('resources/views/vendor/user-management'),
              ], 'views');
              $this->mergeConfigFrom(
                  __DIR__ . '/../../config/config.php', 'user-management'
              );

              $this->mapWebRoutes();

              $this->mapApiRoutes();
          }

          /**
           * Register the service provider.
           *
           * @return void
           */
          public function register()
          {
              $this->app->singleton(\Acioina\UserManagement\Contracts\StateDisplayerContract::class, function($app)
              {
                  return new StateDisplayer(
                      $app['view'], 
                      $app['config']->get('user-management')
                      );
              });

              $this->app->singleton(\Acioina\UserManagement\Contracts\LayoutManagerContract::class, function($app)
              {
                  return new LayoutManager(
                      $app['config']->get('user-management'), 
                      $app['view'], 
                      $app['files'], 
                      $app[\Acioina\UserManagement\Contracts\StateDisplayerContract::class]
                      );
              });
          }

          public function provides()
          {
              return [
                  \Acioina\UserManagement\Contracts\StateDisplayerContract::class,
                  \Acioina\UserManagement\Contracts\LayoutManagerContract::class,
              ];
          }

          /**
           * Define the "web" routes for the application.
           *
           * These routes all receive session state, CSRF protection, etc.
           *
           * @return void
           */
          protected function mapWebRoutes()
          {
              \Route::middleware('web')
                 ->namespace($this->namespace)
                 ->group(function ($router) {
                     require __DIR__ . '/../routes/web.php';
                 });
          }

          /**
           * Define the "api" routes for the application.
           *
           * These routes are typically stateless.
           *
           * @return void
           */
          protected function mapApiRoutes()
          {
              \Route::prefix('api')
                 ->middleware('api')
                 ->namespace($this->namespace)
                 ->group(function ($router) {
                     require __DIR__ . '/../routes/api.php';
                 });
          }

      }