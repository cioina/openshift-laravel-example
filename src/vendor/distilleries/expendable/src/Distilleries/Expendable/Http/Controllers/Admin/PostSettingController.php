<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\PostSetting\PostSettingForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\PostSetting;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class PostSettingController extends ModelBaseController  implements FormStateContract
      {

          use FormStateTrait;

          public function __construct(PostSettingForm $form, PostSetting $model, LayoutManagerContract $layoutManager)
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
              if(! empty($post) && isset($post))
              {
                  PostSetting::where('post_id', '=', $post)->delete();
              }

              $postSettings = $request->get('post_settings');
              if(! empty($postSettings) && isset($postSettings))
              {
                  foreach ($postSettings as $post_id => $settings)
                  {
                      foreach ($settings as $setting_id)
                      {
                          $postSettingModel = new $this->model;
                          $postSettingModel->post_id = $post_id;
                          $postSettingModel->setting_id = $setting_id;
                          $postSettingModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }