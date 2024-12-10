<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Controllers\Base\FrontEndBaseController;
      use Distilleries\Expendable\Models\Client;

      class FrontEndProfileController extends FrontEndBaseController
      {
          public function __construct(LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);
          }
          
          public function getIndex(string $userName = null)
          {
              abort_if($userName === null, 404);
              $client  = Client::where('username','=', $userName)->first();
              abort_if(empty($client), 404);
              
              //TODO: implement this
              //abort_if($client->isDeleted, 404);

              return $this->layoutManager->render();
              
          }
      }