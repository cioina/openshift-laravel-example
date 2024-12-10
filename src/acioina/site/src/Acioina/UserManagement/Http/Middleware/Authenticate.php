<?php namespace Acioina\UserManagement\Http\Middleware;

      use Distilleries\Expendable\Events\UserEvent;
      use Distilleries\Expendable\Helpers\UserUtils;
      use Distilleries\Expendable\Register\ListenerAutoLoader;
      use Distilleries\Expendable\Helpers\TranslationUtils;
      
      use Closure;
      use Illuminate\Contracts\Auth\Guard;
      use Illuminate\Contracts\Config\Repository;
      use Illuminate\Http\Request;

      class Authenticate {

          /**
           * The Guard implementation.
           *
           * @var Guard
           */
          protected $auth;
          protected $config;

          /**
           * Create a new filter instance.
           *
           * @param  Guard $auth
           */
          public function __construct(Guard $auth, Repository $config)
          {
              $this->auth   = $auth;
              $this->config = $config;
          }

          /**
           * Handle an incoming request.
           *
           * @param  \Illuminate\Http\Request $request
           * @param  \Closure $next
           * @return mixed
           */
          public function handle(Request $request, Closure $next)
          {
              if (! $request->hasSession())
              {
                  return response('Server Error.', 500);
              }

              if($request->session()->has(TranslationUtils::KEY_LOCALE))
              {
                  app()->setLocale($request->session()->get(TranslationUtils::KEY_LOCALE));
              }

              $permissionsArea = UserUtils::getArea();
              if ( empty($permissionsArea) )
              {
                  $this->auth->logout();
                  UserUtils::forgotIsLoggedIn();
              }

              if ($this->auth->guest())
              {
                  if ($request->ajax())
                  {
                      return response('Unauthorized.', 401);
                  } 
                  else
                  {
                      //$credential = [ 
                      //    'email'      => $GLOBALS['CIOINA_Config']->get('LaravelWebUser'),
                      //    'password'   => $GLOBALS['CIOINA_Config']->get('LaravelWebPassword')
                      //];

                      // public function attempt(array $credentials = [], $remember = false)
                      // $remember = true generates cookies remember_web_blablabla...
                      //if ($this->auth->attempt($credential))
                      if ($this->auth->loginUsingId(2))
                      {
                          $autoLoaderListener = new ListenerAutoLoader(config('user-management.listener'));
                          $autoLoaderListener->load();

                          $user = $this->auth->user();
                          new UserEvent(UserEvent::LOGIN_EVENT, $user);

                          $actionName = $request->route()->getActionName();
                          if ($actionName != 'Closure')
                          {
                              return $next($request);
                          }
                          
                          //$menu = config('user-management.menu');
                          //if (method_exists($user, 'getFirstRedirect'))
                          //{
                          //    $firsttRedirect = $user->getFirstRedirect($menu['left']);
                          //    if(empty($firsttRedirect))
                          //    {
                          //        abort(403);
                          //    }
                          //    return redirect()->to($firsttRedirect, 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
                          //}
                          return redirect()->to($GLOBALS['CIOINA_Config']->get('HomePage'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
                      }else{
                          abort(403);
                      }
                  }
              }
              return $next($request);
          }
      }