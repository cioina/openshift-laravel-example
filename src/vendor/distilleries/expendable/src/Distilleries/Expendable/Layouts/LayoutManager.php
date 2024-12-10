<?php namespace Distilleries\Expendable\Layouts;

      use Closure;
      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Contracts\StateDisplayerContract;
      use Distilleries\Expendable\Models\Client;
      use Illuminate\Contracts\View\Factory;
      use Illuminate\Filesystem\Filesystem;
      use \Facebook\Facebook;
      use \Facebook\Exceptions\FacebookSDKException;
      use \Facebook\Exceptions\FacebookResponseException;
      use \Facebook\Authentication\AccessToken;

      class LayoutManager implements LayoutManagerContract 
      {
          // These are "global" keys
          const KEY_SESSION_LOGIN_URL  = 'loginUrl';
          const KEY_SESSION_LOGOUT_URL = 'logoutUrl';
          const KEY_SESSION_FB_TOKEN   = 'fb_token';
          const KEY_SESSION_FB_PICTURE = 'fb_picture';

          protected $config;
          protected $view;
          protected $state;
          protected $filesystem;
          protected $items = [];
          protected $layout = null;
          protected $accessToken = null;

          public function __construct(array $config, Factory $view, Filesystem $filesystem, StateDisplayerContract $state)
          {
              $this->config     = $config;
              $this->view       = $view;
              $this->filesystem = $filesystem;
              $this->state      = $state;
          }
          
          /**
           * @codeCoverageIgnore
           */ 
          public function checkFacebook()
          {
              unset($_SESSION[self::KEY_SESSION_LOGIN_URL]);
              unset($_SESSION[self::KEY_SESSION_LOGOUT_URL]);

              try {
                  $fb = new Facebook([
                 'app_id'     => $GLOBALS['CIOINA_Config']->get('FacebookAppId'),
                 'app_secret' => $GLOBALS['CIOINA_Config']->get('FacebookAppSecret'),
                 'default_graph_version' => $GLOBALS['CIOINA_Config']->get('FacebookGraphVersion')]);
              }
              catch(Exception $e) {
                  $CIOINA_fatalError('Incorrect Facebook settings.');
              }

              try 
              {
                  $fb->setDefaultAccessToken($this->accessToken);
              }
              catch(FacebookSDKException $e) {
                  CIOINA_fatalError('OAuth 2.0 client handler');
              }

              try 
              {
                  $response = $fb->get('/me?fields=email,first_name,last_name');
                  $node = $response->getGraphNode();

                  $res = $fb->get( '/me/picture?type=large&redirect=false' );
                  $picture = $res->getGraphObject();

                  //This is a simple tracking method for connected Facebook user.
                  $_SESSION[self::KEY_SESSION_FB_PICTURE] = $picture['url'];

                  $client = Client::findOrFail(1);
                  
                  if ($node->getField('id')    ==  $client->fb_id && 
                      $node->getField('email') ==  $client->fb_email)
                  {
                      return true;
                  }
              }
              catch(FacebookResponseException $e) {
                  CIOINA_fatalError('Graph returned an error: ' . $e->getMessage());
              }
              catch(FacebookSDKException $e) {
                  CIOINA_fatalError('Facebook SDK returned an error');
              }
              
              return false;
          }
          
          /**
           * @codeCoverageIgnore
           */
          public function makeFacebookForm()
          {
              if ( isset( $_SESSION[self::KEY_SESSION_FB_TOKEN] ) ) 
              {
                  $this->accessToken = $_SESSION[self::KEY_SESSION_FB_TOKEN];
                  if (!$this->accessToken instanceof AccessToken) 
                  {
                      $this->accessToken = null;
                  }
                  elseif ($this->accessToken->isExpired())
                  {
                      $this->accessToken = null;
                  }
              } else {
                  $this->accessToken = null;
              }

              if ( !isset( $this->accessToken ) ) 
              { 
                  $buttonText = 'expendable::form.facebook_button_login';
                  $titleForm = 'expendable::form.facebook_title_login';
                  $closeMessage = 'expendable::form.facebook_close_message_login';

                  if ( isset( $_SESSION[self::KEY_SESSION_LOGIN_URL] ) ) 
                  {
                      $facebookLink = $_SESSION[self::KEY_SESSION_LOGIN_URL];
                  }
                  elseif ( isset( $_SESSION[self::KEY_SESSION_LOGOUT_URL] ) ) 
                  {
                      $facebookLink = $_SESSION[self::KEY_SESSION_LOGOUT_URL];
                      $titleForm = 'expendable::form.facebook_title_logout';
                      $buttonText = 'expendable::form.facebook_button_logout';
                      $closeMessage = 'expendable::form.facebook_close_message_logout';
                  }
                  else{
                      define('ACIOINA_FACEBOOK', true);
                      require_once( 'facebookini.php' );
                      if ( isset( $_SESSION[self::KEY_SESSION_LOGIN_URL] ) ) 
                      {
                          $facebookLink = $_SESSION[self::KEY_SESSION_LOGIN_URL];
                      }elseif ( isset( $_SESSION[self::KEY_SESSION_LOGOUT_URL] ) ) 
                      {
                          $facebookLink = $_SESSION[self::KEY_SESSION_LOGOUT_URL];
                          $titleForm = 'expendable::form.facebook_title_logout';
                          $buttonText = 'expendable::form.facebook_button_logout';
                          $closeMessage = 'expendable::form.facebook_close_message_logout';
                      }  
                  }

                  $content = $this->view->make('expendable::admin.part.facebook')->with([
                        'id' => uniqid(),
                        'version' => $GLOBALS['CIOINA_Config']->get('CacheVersion'),
                        'facebookLink' => $facebookLink,
                        'buttonText' => $buttonText,
                        'titleForm' => $titleForm,
                        'closeMessage' => $closeMessage,
                        'modalTitle' => 'expendable::form.facebook_modal_title',
                        'modalMessage' => 'expendable::form.facebook_modal_message',
                  ]);
                  return  $content;
              }
              return null;
          }

          public function setupLayout($layout)
          {
              $this->layout = $layout;
          }

          public function initInterfaces(array $interfaces, $class)
          {

              foreach ($interfaces as $interface)
              {
                  if (strpos($interface, 'StateContract') !== false)
                  {
                      $this->state->setState($interface);
                  }
              }

              $this->state->setClass($class);
          }

          public function initStaticPart(Closure $closure = null)
          {
              if (!is_null($this->layout))
              {
                  $version =  $this->isPhpUnit() ? 'phpunit' : $GLOBALS['CIOINA_Config']->get('CacheVersion');

                  $header = $this->view->make('expendable::admin.part.header')->with([
                      'version' => $version,
                      'title'   => ''
                  ]);
                  $footer = $this->view->make('expendable::admin.part.footer')->with([
                      'version' => $version,
                      'title'   => ''
                  ]);

                  $this->add([
                      'header' => $header,
                      'footer' => $footer,
                  ]);

                  if (!empty($closure))
                  {
                      $closure($this);
                  }

              }
          }

          public function add(array $items)
          {
              $this->items = array_merge($this->items, $items);
          }

          public function render()
          {
              return $this->view->make($this->layout, $this->items);
          }

          /**
           * @return array
           */
          public function getConfig()
          {
              return $this->config;
          }

          /**
           * @return Factory
           */
          public function getView()
          {
              return $this->view;
          }

          /**
           * @return StateDisplayerContract
           */
          public function getState()
          {
              return $this->state;
          }

          /**
           * @return Filesystem
           */
          public function getFilesystem()
          {
              return $this->filesystem;
          }
          
          /**
           * @codeCoverageIgnore
           */
          public function getAccessToken()
          {
              return $this->accessToken;
          }
          
          public function isPhpUnit()
          {
              $phpunit = false;
              if(PHP_SAPI == 'cli') 
              {
                  if(strpos($_SERVER['argv'][0], 'phpunit') !== false) 
                  { 
                      $phpunit = true;
                  }
              }  
              return $phpunit;
          }

      }