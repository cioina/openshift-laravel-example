<?php

namespace Distilleries\FormBuilder;

use Illuminate\Foundation\AliasLoader;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\FormBuilder;
use Collective\Html\FormBuilder as LaravelForm;
use Collective\Html\HtmlBuilder as LaravelHtml;
use Illuminate\Support\ServiceProvider;

class FormBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../views', 'form-builder');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'form-builder');

        $this->publishes([__DIR__ . '/../../config/config.php' => config_path('form-builder.php')]);
        $this->publishes([__DIR__ . '/../../views' => base_path('resources/views/vendor/form-builder')], 'views');

        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'form-builder');
    }

    public function register()
    {
        $this->commands(\Kris\LaravelFormBuilder\Console\FormMakeCommand::class);

        $this->registerHtmlIfNeeded();
        $this->registerFormIfHeeded();

        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'laravel-form-builder');

        $this->registerFormHelper();

        $this->app->singleton('laravel-form-builder', function ($app) {
            return new FormBuilder($app, $app['laravel-form-helper']);
        });

        $this->commands(\Distilleries\FormBuilder\Console\FormMakeCommand::class);

        $this->alias();
    }

    protected function registerFormHelper()
    {
        $this->app->singleton('laravel-form-helper', function ($app) {
            $config = $app['config']->get('form-builder');
            return new FormHelper($app['view'], $app['request'], $config);
        });

        $this->app->alias('laravel-form-helper', 'Kris\LaravelFormBuilder\FormHelper');
    }

    private function registerHtmlIfNeeded()
    {
        if (! $this->app->offsetExists('html')) {
            $this->app->singleton('html', function ($app) {
                return new LaravelHtml($app['url'], $app['view']);
            });

            $this->registerAliasIfNotExists('Html', \Collective\Html\HtmlFacade::class);
        }
    }

    private function registerFormIfHeeded()
    {
        if (! $this->app->offsetExists('form')) {
            $this->app->singleton('form', function ($app) {
                $form = new LaravelForm($app['html'], $app['url'], $app['view'], $app['session.store']->token());
                return $form->setSessionStore($app['session.store']);
            });

            $this->registerAliasIfNotExists('Form', \Collective\Html\FormFacade::class);
        }
    }

    private function alias()
    {
        $this->registerAliasIfNotExists('FormBuilder', Facades\FormBuilder::class);
        $this->registerAliasIfNotExists('Request', \Illuminate\Support\Facades\Request::class);
        $this->registerAliasIfNotExists('Route', \Illuminate\Support\Facades\Route::class);
        $this->registerAliasIfNotExists('File', \Illuminate\Support\Facades\File::class);
        $this->registerAliasIfNotExists('Redirect', \Illuminate\Support\Facades\Redirect::class);
    }

    private function registerAliasIfNotExists($alias, $class)
    {
        if (! array_key_exists($alias, AliasLoader::getInstance()->getAliases())) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

    /**
     * @return string[]
     */
    public function provides()
    {
        return ['laravel-form-builder'];
    }

}
