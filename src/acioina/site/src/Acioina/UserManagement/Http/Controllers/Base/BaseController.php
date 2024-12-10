<?php namespace Acioina\UserManagement\Http\Controllers\Base;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Controllers\Controller as AppController;

      class BaseController extends AppController 
      {
          protected $layoutManager;
          protected $layout = 'user-management::user.layout.default';

          public function __construct(LayoutManagerContract $layoutManager)
          {
              $this->layoutManager = $layoutManager;
              $this->setupLayout();
          }

          protected function setupLayout()
          {
              $this->layoutManager->setupLayout($this->layout);
              $this->setupStateProvider();
              $this->initStaticPart();
          }

          protected function setupStateProvider()
          {
              $interfaces = class_implements($this);
              $this->layoutManager->initInterfaces($interfaces, get_class($this));
          }

          protected function initStaticPart()
          {
              $this->layoutManager->initStaticPart(function($layoutManager)
              {
                  $menu_top  = $layoutManager->getView()->make('user-management::user.menu.top');
                  $menu_left = $layoutManager->getView()->make('user-management::user.menu.left');

                  $layoutManager->add([
                      'state.menu' => $layoutManager->getState()->getRenderStateMenu(),
                      'menu_top'   => $menu_top,
                      'menu_left'  => $menu_left
                  ]);
              });
          }
      }