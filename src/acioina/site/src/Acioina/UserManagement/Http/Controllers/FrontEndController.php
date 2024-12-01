<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Controllers\Base\FrontEndBaseController;

      class FrontEndController extends FrontEndBaseController
      {
          public function __construct(LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);
          }
      }