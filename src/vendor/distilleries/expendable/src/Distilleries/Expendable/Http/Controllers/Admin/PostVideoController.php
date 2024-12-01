<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\PostVideo\PostVideoForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\PostVideo;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class PostVideoController extends ModelBaseController  implements FormStateContract 
      {

          use FormStateTrait;

          public function __construct(PostVideoForm $form, PostVideo $model, LayoutManagerContract $layoutManager)
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

              $post = $request->get($this->model->getKeyName());
              if(isset($post))
              {
                  PostVideo::where('post_id', '=', $post)->delete();
              }

              $postVideos = $request->get('post_videos');
              if(isset($postVideos))
              {
                  foreach ($postVideos as $post_id => $youtubeVideos) 
                  {
                      foreach ($youtubeVideos as $video_id)
                      {
                          $postVideoModel = new $this->model;
                          $postVideoModel->post_id = $post_id;
                          $postVideoModel->video_id = $video_id;
                          $postVideoModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }