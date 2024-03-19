<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Controllers\Base\FrontEndBaseController;
      use Distilleries\Expendable\Models\Post;

      class FrontEndArticleController extends FrontEndBaseController
      {
          public function __construct(LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);
          }

          public function getIndex(string $slug = null)
          {
              abort_if($slug === null, 404);
              $post  = Post::where('slug','=', $slug)->first();
              abort_if(empty($post), 404);

              return $this->layoutManager->render();

          }
      }