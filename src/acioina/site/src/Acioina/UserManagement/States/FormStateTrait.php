<?php namespace Acioina\UserManagement\States;

      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Models\Language;
      use Distilleries\Expendable\Helpers\TranslationUtils;
      
      use Illuminate\Http\Request;
      use Illuminate\Support\Str;
      use \FormBuilder;

      trait FormStateTrait {

          /**
           * @var \Kris\LaravelFormBuilder\Form $form
           * Injected by the constructor
           */
          protected $form;

          protected function isTranslatableModel()
          {
              return method_exists($this->model, 'withoutTranslation');
          }

          protected function findAutoDetectTranslation($id, $orfail = true)
          {
              if ($orfail) 
              {
                  if(! is_numeric($id))
                  {
                      if ($this->isTranslatableModel()) 
                      {
                          return $this->model->withoutTranslation()->where('slug', '=', Str::slug($id, "-"))->firstOrFail();
                      } else {
                          return $this->model->where('slug', '=', Str::slug($id, "-"))->firstOrFail();
                      }
                  }else{
                      if ($this->isTranslatableModel()) 
                      {
                          return $this->model->withoutTranslation()->findOrFail($id);
                      } else {
                          return $this->model->findOrFail($id);
                      }
                  }
              } else {
                  if (! is_numeric($id)) 
                  {
                      if ($this->isTranslatableModel()) 
                      {
                          return $this->model->withoutTranslation()->where('slug', '=', Str::slug($id, "-"))->first();
                      } else {
                          return $this->model->where('slug', '=', Str::slug($id, "-"))->first();
                      }
                  }else{
                      if ($this->isTranslatableModel()) 
                      {
                          return $this->model->withoutTranslation()->find($id);
                      } else {
                          return $this->model->find($id);
                      }
                  }
              }

              return null;
          }
          
          public function getView($id)
          {
              $model = (!empty($id)) ? $this->findAutoDetectTranslation($id) : $this->model;
              
              if ($this->isTranslatableModel()) 
              {
                  $local_overide = $this->model->getIso($model->getTable(), $model->id);
                  if (! empty($local_overide)) 
                  {
                      $locale = Language::where('iso','like', $local_overide . '%')->first();
                      if (empty($locale))
                      {
                          abort(404);
                      }

                      if($local_overide !== app()->getLocale())
                      {
                          TranslationUtils::setRedirectBack(
                          [
                              'id'=> $model->id,
                              'controller'=> $this->getControllerNameForAction().'@getView',
                          ]);

                          return redirect()->to(action('\Acioina\UserManagement\Http\Controllers\LanguageMenuController@getIndex',[$local_overide]), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
                      }
                  }
              }
              
              $this->model = $this->model->where('status', '=', StaticLabel::STATUS_ONLINE);
              $form  = FormBuilder::create(get_class($this->form), [
                  'model' => $model,
              ]);

              $form_content = view('user-management::user.form.components.formgenerator.info', [
                  'form'  => $form,
                  'id'    => $id,
                  'route' => $this->getControllerNameForAction() . '@',
              ]);

              $this->layoutManager->add([
                  'content' => view('user-management::user.form.state.form', [
                      'form' => $form_content
                  ])
              ]);

              return $this->layoutManager->render();
          }

          protected function getControllerNameForAction()
          {
              $action = explode('@', \Route::currentRouteAction());

              return '\\' . $action[0];
          }
      }