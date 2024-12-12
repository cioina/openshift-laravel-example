<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\WebPageImage\WebPageImageForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\WebPageImage;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class WebPageImageController extends ModelBaseController  implements FormStateContract
      {

          use FormStateTrait;

          public function __construct(WebPageImageForm $form, WebPageImage $model, LayoutManagerContract $layoutManager)
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

              $webPage = $request->get($this->model->getKeyName());
              if(! empty($webPage) && isset($webPage))
              {
                  WebPageImage::where('web_page_id', '=', $webPage)->delete();
              }

              $blogImages = $request->get('web_page_images');
              if(! empty($blogImages) && isset($blogImages))
              {
                  foreach ($blogImages as $post_id=>$facebookImages)
                  {
                      foreach ($facebookImages as $image_id)
                      {
                          $blogImageModel = new $this->model;
                          $blogImageModel->web_page_id = $post_id;
                          $blogImageModel->image_id = $image_id;
                          $blogImageModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\WebPageController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }