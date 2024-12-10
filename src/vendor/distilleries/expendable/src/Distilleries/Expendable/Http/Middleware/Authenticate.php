<?php namespace Distilleries\Expendable\Http\Middleware;

      use Distilleries\Expendable\Helpers\TranslationUtils;
      use Closure;
      use Illuminate\Contracts\Auth\Guard;
      use Illuminate\Contracts\Config\Repository;

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
          public function handle($request, Closure $next)
          {
              if (! $request->hasSession())
              {
                  return response('Server Error.', 500);
              }

              if($request->session()->has(TranslationUtils::KEY_LOCALE))
              {
                  app()->setLocale($request->session()->get(TranslationUtils::KEY_LOCALE));
              }

              if ($this->auth->guest())
              {
                  if ($request->ajax())
                  {
                      return response('Unauthorized.', 401);
                  } 
                  else
                  {
                      $baseUrl = $request->segment(1);
                      if( isset($baseUrl) && $baseUrl === $this->config->get('expendable.admin_base_uri') )
                      {
                          return redirect()->guest($this->config->get('expendable.login_uri'));
                      }
                  }
              }
              
              return $next($request);
          }
      }