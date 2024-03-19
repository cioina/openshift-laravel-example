<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\WebPageVideo\WebPageVideoForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\WebPageVideo;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class WebPageVideoController extends ModelBaseController  implements FormStateContract 
      {

          use FormStateTrait;

          public function __construct(WebPageVideoForm $form, WebPageVideo $model, LayoutManagerContract $layoutManager)
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
              if(isset($webPage))
              {
                  WebPageVideo::where('web_page_id', '=', $webPage)->delete();
              }

              $webpageVideos = $request->get('web_page_videos');
              if(isset($webpageVideos))
              {
                  foreach ($webpageVideos as $webpage_id=>$youtubeVideos) 
                  {
                      foreach ($youtubeVideos as $video_id)
                      {
                          $webpageVideoModel = new $this->model;
                          $webpageVideoModel->web_page_id = $webpage_id;
                          $webpageVideoModel->video_id = $video_id;
                          $webpageVideoModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\WebPageController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }