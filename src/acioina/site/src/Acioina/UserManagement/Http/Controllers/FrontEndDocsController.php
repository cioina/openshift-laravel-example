<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Controllers\Base\FrontEndBaseController;

      class FrontEndDocsController extends FrontEndBaseController
      {
          public function __construct(LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);
          }
          
          public function getIndex(string $name = null)
          {
              abort_if($name === null, 404);

              $json = json_decode('{
"intro"     : [
  {
    "path": "docs/what-is-this",
    "label": "What Is This?",
    "order": 0
  },
  {
    "path": "docs/about-this-blog",
    "label": "About This Blog",
    "order": 1
  }
]
              }');

              $found = false;
              foreach ($json->intro as $key => $value) 
              {
                  if($value->path === "docs/$name" )
                  {
                      $found = true;
                      break;
                  }
              }

              abort_if(!$found, 404);

              return $this->layoutManager->render();
              
          }
      }