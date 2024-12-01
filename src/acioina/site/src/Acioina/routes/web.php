<?php

//<link media="all" type="text/css" rel="stylesheet" href="{{ route('resource',['path' => 'admin/css/app.admin.min.css', 'v' => $version,]) }}" />
//$router->get('/assets/{path}', 'ResourceController@getIndex')->where('path', '.*')->name('resource');

$router->group(['middleware' => 'Acioina\UserManagement\Http\Middleware\Authenticate'],
    function() use($router)
     {
         $router->group(['middleware' => 'Distilleries\PermissionUtil\Http\Middleware\CheckAccessPermission'],
            function() use($router)
            {
                if (app()->environment(['local','testing']))
                {
                    $router->get('/logout', 'ClientLogoutController@getIndex');
                    $router->get('/cookies', 'ClientLogoutController@getCookies');

                    $router->get('/set-lang/{locale?}', 'LanguageMenuController@getIndex');

                    $router->get('/select/edit/{id?}', 'SelectController@getEdit');

                    $router->get('/contact/edit/{id?}', 'GuestEmailController@getEdit');
                    $router->post('/contact/edit', 'GuestEmailController@postEdit');

                    $router->get('/client/edit/{id?}', 'ClientLoginController@getEdit');
                    $router->post('/client/edit', 'ClientLoginController@postEdit');

                    $router->get('/survey/edit/{id?}', 'SurveyController@getEdit');
                    $router->post('/survey/edit', 'SurveyController@postEdit');

                    $router->controller('/page', 'WebPageController');
                    $router->controller('/blog', 'PostController');
                    $router->controller('/image', 'FacebookImageController');
                }
               
                 $router->get('/', 'FrontEndController@getIndex');
                 $router->get('/register', 'FrontEndController@getIndex');
                 $router->get('/sign-in', 'FrontEndController@getIndex');
                 $router->get('/articles', 'FrontEndController@getIndex');

                 $router->get('/docs', 'FrontEndController@getIndex');
                 $router->get('/docs/{name?}', 'FrontEndDocsController@getIndex');
                 $router->get('/profile/{userName?}', 'FrontEndProfileController@getIndex');
                 $router->get('/articles/{slug?}', 'FrontEndArticleController@getIndex');
            }
         );
     }
);

\View::composer('user-management::user.layout.default', function($view)
{
    $view->with('title', '')
         ->with('user', 'webUser');
});