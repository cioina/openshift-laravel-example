<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Models\Client;
      use Distilleries\Expendable\Helpers\UserUtils;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Events\UserEvent;
      use Distilleries\Expendable\Formatter\Message;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseController;
      use Illuminate\Http\Request;
      use Illuminate\Contracts\Auth\Guard;
      use Illuminate\Contracts\Auth\PasswordBroker;
      use Illuminate\Auth\Passwords\CanResetPassword;
      use FormBuilder;
      use \CIOINA_Util;

      class LoginController extends BaseController
      {
          use CanResetPassword;

          protected $layout = 'expendable::admin.layout.login';

          /**
           * Create a new password controller instance.
           *
           * @param  \Illuminate\Contracts\Auth\Guard $auth
           * @param  \Illuminate\Contracts\Auth\PasswordBroker $passwords
           * @param  \Distilleries\Expendable\Contracts\LayoutManagerContract $layoutManager
           */
          public function __construct(Guard $auth, PasswordBroker $passwords, LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);

              $this->auth      = $auth;
              $this->passwords = $passwords;
          }

          public function getIndex()
          {
              // @codeCoverageIgnoreStart

              if(!$GLOBALS['CIOINA_Config']->get('ExpendableServiceProviderEnabled'))
              {
                  $fb_settings = FormUtils::getFacebookSettings();

                  if(! $this->layoutManager->isPhpUnit() &&
                      ($fb_settings !== false) &&
                      $fb_settings->data->IsFacebookEnabled)
                  {
                      $content = $this->layoutManager->makeFacebookForm();
                      $token = $this->layoutManager->getAccessToken();
                      if ( ! isset( $token ) )
                      {
                          UserUtils::forgotIsLoggedIn();
                          UserUtils::forgotArea();
                          $this->layoutManager->add([
                                'class_layout' => 'login',
                                'content'      => $content,
                            ]);
                          return $this->layoutManager->render();
                      }

                      if (! $this->layoutManager->checkFacebook())
                      {
                          $this->layoutManager->add([
                              'class_layout' => 'login',
                              'content'      => $content,
                          ]);
                          return $this->layoutManager->render();
                      }
                  }
              }
              // @codeCoverageIgnoreEnd

              $form = FormBuilder::create('Distilleries\Expendable\Forms\Login\SignIn', [
                  'class' => 'login-form',
                  'url' => FormUtils::tryHttps(action('\\Distilleries\\Expendable\\Http\\Controllers\\Admin\\LoginController@postIndex')),
              ]);

              $content = view('expendable::admin.login.signin', [
                  'form' => $form
              ]);

              $this->layoutManager->add([
                  'class_layout' => 'login',
                  'content'      => $content,
              ]);

              return $this->layoutManager->render();
          }

          public function postIndex(Request $request)
          {
              // @codeCoverageIgnoreStart
              if(!$GLOBALS['CIOINA_Config']->get('ExpendableServiceProviderEnabled'))
              {

                  $fb_settings = FormUtils::getFacebookSettings();

                  if(! $this->layoutManager->isPhpUnit() &&
                      ($fb_settings !== false) &&
                      $fb_settings->data->IsFacebookEnabled)
                  {
                      $this->layoutManager->makeFacebookForm();
                      $token = $this->layoutManager->getAccessToken();
                      if ( ! isset($token))
                      {
                          return redirect()->back()->with(Message::WARNING, [trans('expendable::login.credential')]);
                      }

                      if (! $this->layoutManager->checkFacebook())
                      {
                          return redirect()->back()->with(Message::WARNING, [trans('expendable::login.credential')]);
                      }
                  }else{
                      $client = Client::findOrFail(1);
                      if($client->request_ip_address !== CIOINA_Util::getIP())
                      {
                          return redirect()->back()->with(Message::WARNING, [trans('expendable::login.credential')]);
                      }

                  }
              }
              // @codeCoverageIgnoreEnd

              $form = FormBuilder::create('Distilleries\Expendable\Forms\Login\SignIn');

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $credential = $request->only('email', 'password');

              // public function attempt(array $credentials = [], $remember = false)
              // $remember = true generates cookies remember_web_blablabla...
              if ($this->auth->attempt($credential))
              {
                  $user = $this->auth->user();
                  new UserEvent(UserEvent::LOGIN_EVENT, $user);

                  $menu = config('expendable.menu');

                  if (method_exists($user, 'getFirstRedirect'))
                  {
                      return redirect()->to($user->getFirstRedirect($menu['left']), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
                  }

                  return redirect()->to('/', 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));

              } else
              {
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::login.credential')]);
              }
          }

          public function getRemind()
          {
              $form = FormBuilder::create('Distilleries\Expendable\Forms\Login\Forgotten', [
                  'class' => 'login-form',
                  'url' => FormUtils::tryHttps(action('\\Distilleries\\Expendable\\Http\\Controllers\\Admin\\LoginController@postRemind')),
              ]);

              $content = view('expendable::admin.login.forgot', [
                  'form' => $form
              ]);

              $this->layoutManager->add([
                  'class_layout' => 'login',
                  'content'      => $content,
              ]);

              return $this->layoutManager->render();

          }

          public function postRemind(Request $request)
          {
              if (! config('expendable.reset_admin_password') )
              {
                  return redirect()->back()->with('error', trans('expendable::passwords.reset_password_disabled'));
              }

              $form = FormBuilder::create('Distilleries\Expendable\Forms\Login\Forgotten');
              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $response = $this->passwords->sendResetLink($request->only('email'));

              switch ($response)
              {
                  case PasswordBroker::INVALID_USER:
                      return redirect()->back()->with(Message::WARNING, [trans( 'expendable::' . $response)]);
                  case PasswordBroker::RESET_LINK_SENT:
                      return redirect()->back()->with(Message::MESSAGE, [trans( 'expendable::' . $response)]);
                  default:
                      return redirect()->back()->with(Message::WARNING, [trans( 'expendable::passwords.unknown_error')]);
              }

          }

          public function getReset($token = null)
          {
              if (is_null($token))
              {
                  abort(404);
              }

              $form = FormBuilder::create('Distilleries\Expendable\Forms\Login\Reset', [
                  'class' => 'login-form',
                  'url' => FormUtils::tryHttps(action('\\Distilleries\\Expendable\\Http\\Controllers\\Admin\\LoginController@postReset')),

              ], [
                  'token' => $token
              ]);

              $content = view('expendable::admin.login.reset', [
                  'form' => $form
              ]);

              $this->layoutManager->add([
                  'class_layout' => 'login',
                  'content'      => $content,
              ]);

              return $this->layoutManager->render();
          }

          public function postReset(Request $request)
          {
              if (! config('expendable.reset_admin_password') )
              {
                  return redirect()->back()->with('error', trans('expendable::passwords.reset_password_disabled'));
              }

              $form = FormBuilder::create('Distilleries\Expendable\Forms\Login\Forgotten');
              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $credentials = $request->only(
                  'email',
                  'password',
                  'password_confirmation',
                  'token'
              );

              $response = $this->passwords->reset($credentials, function($user, $password)
              {
                  $user->password = bcrypt($password);
                  $user->save();
                  $this->auth->login($user);
              });

              switch ($response)
              {
                  case PasswordBroker::INVALID_TOKEN:
                  case PasswordBroker::INVALID_USER:
                      return redirect()->back()->with('error', trans('expendable::' . $response));

                  case PasswordBroker::PASSWORD_RESET:
                      return redirect()->to(route('login.index'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
              }

          }

          public function getLogout()
          {
              new UserEvent(UserEvent::LOGOUT_EVENT, $this->auth->user());

              $this->auth->logout();

              return redirect()->to(route('login.index'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          protected function initStaticPart()
          {
              $this->layoutManager->initStaticPart();
          }
      }