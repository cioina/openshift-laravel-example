<?php

$router->group([
    'prefix'        => config('expendable.admin_base_uri'),
    'middleware'    => 'guest'],
    function() use($router)
    {
        $router->get('',
            function()
            {
                return Redirect::to(route('login.index'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
            }
        );

        $router->controller('login', 'LoginController', [
            'getIndex'  => 'login.index',
            'getRemind' => 'login.remind',
            'getLogout' => 'login.logout',
            'getReset'  => 'login.reset',
        ]);
    }
);

$router->group(['middleware' => 'auth'],
    function() use($router)
    {
        $router->group([
            'middleware' => 'permission',
            'prefix'     => config('expendable.admin_base_uri')],
            function() use($router)
            {
                $router->get('/set-lang/{locale?}', 'LanguageMenuController@getIndex');

                $router->controller('user', 'UserController', [
                    'getProfile' => 'user.profile',
                 ]);

                $router->controller('survey', 'SurveyController');
                $router->controller('online', 'OnlineClientController');
                $router->controller('client', 'ClientController');
                $router->controller('guest-email', 'GuestEmailController');
                $router->controller('us-state', 'UsStateController');
                $router->controller('country', 'CountryController');
                $router->controller('setting', 'SettingController');
                $router->controller('sent-email', 'SentEmailController');
                $router->controller('email-type', 'EmailTypeController');
                $router->controller('statistics', 'WebStatisticsController');
                $router->controller('page', 'WebPageController');
                $router->controller('page-image', 'WebPageImageController');
                $router->controller('page-video', 'WebPageVideoController');
                $router->controller('page-setting', 'WebPageSettingController');
                $router->controller('code', 'CodeBlockController');
                $router->controller('facebook-image', 'FacebookImageController');
                $router->controller('video-type', 'VideoTypeController');
                $router->controller('youtube-video', 'YoutubeVideoController');
                $router->controller('topic', 'TopicController');
                $router->controller('blog',  'PostController');
                $router->controller('post-part',  'PostPartController');
                $router->controller('post-image', 'PostImageController');
                $router->controller('post-topic', 'PostTopicController');
                $router->controller('post-video', 'PostVideoController');
                $router->controller('post-setting', 'PostSettingController');
                $router->controller('email','EmailController');
                $router->controller('component', 'ComponentController');
                $router->controller('role', 'RoleController');
                $router->controller('service', 'ServiceController');
                $router->controller('permission', 'PermissionController');
                $router->controller('language', 'LanguageController');
            }
        );
    }
);

\View::composer('expendable::admin.layout.default', function($view)
{
    $view->with('title', '')
         ->with('user', 'sa');
});
