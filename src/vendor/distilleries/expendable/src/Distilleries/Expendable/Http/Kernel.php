<?php

namespace Distilleries\Expendable\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Spatie\Cors\Cors::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ],

       'api' => [
           'cors',
           'bindings',
       ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => \Distilleries\Expendable\Http\Middleware\Authenticate::class,
        'guest'      => \Distilleries\Expendable\Http\Middleware\RedirectIfAuthenticated::class,
        'permission' => \Distilleries\PermissionUtil\Http\Middleware\CheckAccessPermission::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'auth.api'   => \Acioina\UserManagement\Http\Middleware\AuthenticateWithJWT::class,
        'admin.api'  => \Acioina\UserManagement\Http\Middleware\AuthenticateAdminWithJWT::class,
        'cors'       => \Spatie\Cors\Cors::class,

    ];
}