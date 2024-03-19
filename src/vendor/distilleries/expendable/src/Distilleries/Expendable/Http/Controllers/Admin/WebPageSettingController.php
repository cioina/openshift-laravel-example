<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\WebPageSetting\WebPageSettingForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\WebPageSetting;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class WebPageSettingController extends ModelBaseController  implements FormStateContract 
      {

          use FormStateTrait;

          public function __construct(WebPageSettingForm $form, WebPageSetting $model, LayoutManagerContract $layoutManager)
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
                  WebPageSetting::where('web_page_id', '=', $webPage)->delete();
              }

              $pageSettings = $request->get('web_page_settings');
              if(isset($pageSettings))
              {
                  foreach ($pageSettings as $page_id=>$settings) 
                  {
                      foreach ($settings as $setting_id)
                      {
                          $pageSettingModel = new $this->model;
                          $pageSettingModel->web_page_id = $page_id;
                          $pageSettingModel->setting_id = $setting_id;
                          $pageSettingModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\WebPageController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }