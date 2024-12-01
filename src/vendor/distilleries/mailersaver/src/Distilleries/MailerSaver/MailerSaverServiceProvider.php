<?php namespace Distilleries\MailerSaver;

      use Distilleries\MailerSaver\Helpers\Mail;
      use Distilleries\MailerSaver\Contracts\MailModelContract;
      use Distilleries\Expendable\Models\Email;

      class MailerSaverServiceProvider extends \Illuminate\Mail\MailServiceProvider 
      {
          /**
           * Bootstrap the application events.
           *
           * @return void
           */
          public function boot()
          {
              $this->loadViewsFrom(__DIR__ . '/../../views', 'mailersaver');

              $this->publishes([
                  __DIR__ . '/../../config/config.php' => config_path('mailersaver.php'),
              ]);

              $this->publishes([
                  __DIR__ . '/../../views' => base_path('resources/views/vendor/mailersaver'),
              ], 'views');

              // We need this if we do not have an Email.php model yet. 
              //$this->publishes([
              //    __DIR__ . '/../../models/Email.php' => base_path('resources/tmp/Email.php'),
              //], 'models');


              $this->publishes([
                  __DIR__ . '/../../database/migrations/' => base_path('/database/migrations')
              ], 'migrations');


              $this->mergeConfigFrom( __DIR__ . '/../../config/config.php', 'mailersaver');

          }

          public function register()
          {
              $this->app->singleton(\Distilleries\MailerSaver\Contracts\MailModelContract::class, function($app)
              {
                  return new Email;
              });

              $this->registerIlluminateMailer();
              $this->registerCustomMailer();
          }

          
          /**
           * Register the Illuminate mailer instance.
           *
           * @return void
           */
          protected function registerCustomMailer()
          {
              $this->app->singleton('mailer-saver', function($app)
              {
                  $mailer = new Mail(
                      $app[\Distilleries\MailerSaver\Contracts\MailModelContract::class], 
                      $app['config'],
                      'mailgun',
                      $app['view'], 
                      $app['mail.manager'], 
                      $app['events']
                  );

                  $from = $app['config']['mail.from'];

                  if (is_array($from) && isset($from['address']))
                  {
                      $mailer->alwaysFrom($from['address'], $from['name']);
                  }

                  return $mailer;
              });
          }

          /**
           * Get the services provided by the provider.
           *
           * @return array
           */
          public function provides()
          {
              return [
                  'mail.manager',
                  'mailer',
                  //'swift.mailer',
                  //'swift.transport',
                  'mailer-saver',
              ];
          }

      }