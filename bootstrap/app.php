<?php
if (php_sapi_name() !== 'cli')
{
    require_once __DIR__ . '/../public/libraries/laravel.inc.acioina.php';
}
elseif(! defined('INCLUDE_PATH'))
{
    $temp = getcwd();
    $tmp = get_include_path();
    set_include_path($tmp . PATH_SEPARATOR . $temp . DIRECTORY_SEPARATOR . 'public');
    chdir($temp . DIRECTORY_SEPARATOR . 'public');
    require_once getcwd()  . '/libraries/laravel.inc.acioina.php';
    chdir($temp);
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
 */

$app = new Acioina\UserManagement\Fondation\Application(
    realpath(__DIR__.'/../')
);

$app->bind('path.storage', function ($app) {
    return base_path() . DIRECTORY_SEPARATOR . $GLOBALS['CIOINA_Config']->get('LaravelStorage');
});

$app->bind('cache.store', function ($app) {
     return $app ->storagePath() . DIRECTORY_SEPARATOR . 'framework';
});


/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
 */

if (php_sapi_name() === 'cli')
{
    $app->singleton(
        Illuminate\Contracts\Http\Kernel::class,
        Distilleries\Expendable\Http\Kernel::class
    );

    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        Acioina\UserManagement\Console\Kernel::class
    );
}else{
    if( ( (isset($_SESSION) 
            && isset($_SESSION['ExpendableServiceProviderEnabledCounter']) 
            && $_SESSION['ExpendableServiceProviderEnabledCounter'] == 10)
        ) 
        || ( (isset($_SESSION)  
              && $GLOBALS['CIOINA_Config']->get('ExpendableServiceProviderEnabled')
        )))
    {
        $app->singleton(
            Illuminate\Contracts\Http\Kernel::class,
            Distilleries\Expendable\Http\Kernel::class
        );

        $app->singleton(
            Illuminate\Contracts\Console\Kernel::class,
            Acioina\UserManagement\Console\Kernel::class
        );
    }else{
        $app->singleton(
          Illuminate\Contracts\Http\Kernel::class,
          Acioina\UserManagement\Http\Kernel::class
      );
    }
}

$app->singleton(
     Illuminate\Contracts\Debug\ExceptionHandler::class,
     Acioina\UserManagement\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
 */

//$app->bootstrapWith([
//\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
//\Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
//\Illuminate\Foundation\Bootstrap\HandleExceptions::class,
//\Illuminate\Foundation\Bootstrap\RegisterFacades::class,
//\Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
//\Illuminate\Foundation\Bootstrap\RegisterProviders::class,
//\Illuminate\Foundation\Bootstrap\BootProviders::class,
//]);

return $app;
