<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\PostImage\PostImageForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Distilleries\Expendable\Models\PostImage;
      use Distilleries\FormBuilder\Contracts\FormStateContract;

      use Illuminate\Http\Request;
      use \FormBuilder;

      class PostImageController extends ModelBaseController  implements FormStateContract
      {

          use FormStateTrait;

          public function __construct(PostImageForm $form, PostImage $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);
              $this->form = $form;
          }

          public function getIndex()
          {
              return redirect()->to(action('\\' . get_class($this) . '@getEdit'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          public function postEdit(Request $request)
          {
              $form = FormBuilder::create(get_class($this->form), [
                  'model' => $this->model,
              ]);

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $blog = $request->get($this->model->getKeyName());
              if(! empty($blog) && isset($blog))
              {
                  PostImage::where('post_id', '=', $blog)->delete();
              }

              $blogImages = $request->get('post_images');
              if(! empty($blogImages) && isset($blogImages))
              {
                  foreach ($blogImages as $post_id=>$facebookImages)
                  {
                      foreach ($facebookImages as $image_id)
                      {
                          $blogImageModel = new $this->model;
                          $blogImageModel->post_id = $post_id;
                          $blogImageModel->image_id = $image_id;
                          $blogImageModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }