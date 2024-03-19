<?php namespace Acioina\UserManagement\Layouts;

      use Closure;
      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Contracts\StateDisplayerContract;
      use Illuminate\Contracts\View\Factory;
      use Illuminate\Filesystem\Filesystem;

      class LayoutManager implements LayoutManagerContract
      {
          protected $config;
          protected $view;
          protected $state;
          protected $filesystem;
          protected $items = [];
          protected $layout = null;

          public function __construct(array $config, Factory $view, Filesystem $filesystem, StateDisplayerContract $state)
          {
              $this->config     = $config;
              $this->view       = $view;
              $this->filesystem = $filesystem;
              $this->state      = $state;
          }

          public function setupLayout($layout)
          {
              $this->layout = $layout;
          }

          public function initInterfaces(array $interfaces, $class)
          {
              if (app()->environment(['local','testing']))
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
          }

          public function initStaticPart(Closure $closure = null)
          {
              if (app()->environment(['local','testing']))
              {
                  if (!is_null($this->layout))
                  {
                      $version = $GLOBALS['CIOINA_Config']->get('CacheVersion');

                      $header = $this->view->make('user-management::user.part.header')->with([
                          'version' => $version,
                          'title'   => ''
                      ]);
                      $footer = $this->view->make('user-management::user.part.footer')->with([
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
          }

          public function add(array $items)
          {
              if (app()->environment(['local','testing']))
              {
                  $this->items = array_merge($this->items, $items);
              }
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
      }