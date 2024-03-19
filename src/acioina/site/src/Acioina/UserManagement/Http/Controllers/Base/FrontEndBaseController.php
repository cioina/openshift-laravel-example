<?php namespace Acioina\UserManagement\Http\Controllers\Base;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Controllers\Controller;

      class FrontEndBaseController extends Controller
      {
          protected $layoutManager;
          protected $layout = 'user-management::user.layout.frontend';

          public function __construct(LayoutManagerContract $layoutManager)
          {
              $this->layoutManager = $layoutManager;
              $this->layoutManager->setupLayout($this->layout);
          }
      
          public function getIndex()
          {
              return $this->layoutManager->render();
          }

      }