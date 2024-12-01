<?php namespace Distilleries\Expendable\Http\Middleware;

      use Distilleries\Expendable\Helpers\UserUtils;
      use Closure;
      use Illuminate\Contracts\Auth\Guard;
      use Illuminate\Contracts\Config\Repository;
      use Illuminate\Contracts\Routing\Registrar;
      use Illuminate\Http\RedirectResponse;

      class RedirectIfAuthenticated 
      {
          /**
           * The Guard implementation.
           *
           * @var Guard
           */
          protected $auth;
          protected $router;
          protected $config;

          /**
           * Create a new filter instance.
           *
           * @param  Guard $auth
           */
          public function __construct(Guard $auth, Registrar $router, Repository $config)
          {
              $this->auth   = $auth;
              $this->router = $router;
              $this->config = $config;
          }

          /**
           * Handle an incoming request.
           *
           * @param  \Illuminate\Http\Request $request
           * @param  \Closure $next
           * @return mixed
           */
          public function handle($request, Closure $next)
          {
              $actionName = $this->router->current()->getActionName();             
              if ($actionName == $this->config->get('expendable.logout_action') 
                  || $actionName == $this->config->get('expendable.reset_password_action')
                  || $actionName == $this->config->get('expendable.post_reset_password_action'))
              {
                  return $next($request);
              }

              if ($this->auth->check() )
              {
                  if(! UserUtils::isSuperAdmin() )
                  {
                      $this->auth->logout();
                      UserUtils::forgotIsLoggedIn();
                      UserUtils::forgotArea();

                      $baseUrl = $request->segment(1);
                      if( isset($baseUrl) && $baseUrl === $this->config->get('expendable.admin_base_uri') )
                      {
                          return redirect()->guest($this->config->get('expendable.login_uri'));
                      }

                      abort(403);
                  }

                  $menu = $this->config->get('expendable.menu');
                  $user = $this->auth->user();

                  if (method_exists($user, 'getFirstRedirect'))
                  {
                      $firsttRedirect = $user->getFirstRedirect($menu['left']);
                      if(empty($firsttRedirect))
                      {
                          abort(403);
                      }
                      return new RedirectResponse($firsttRedirect);
                  }
                  return new RedirectResponse('/');
              }
              
              return $next($request);
          }
      }