<?php namespace Acioina\UserManagement\Http\Datatables;

      use Distilleries\DatatableBuilder\EloquentDatatable;
      use Distilleries\Expendable\Models\Language;
      use Distilleries\Expendable\Models\Translation;

      abstract class BaseDatatable extends EloquentDatatable 
      {
          public function addTranslationAction($template = 'user-management::user.form.components.datatable.translations', $route = '')
          {
              $locale = Language::where('iso','like', app()->getLocale() . '%')->where('is_default','=', 1)->first();
              if (! empty($locale)) 
              {
                  $languages = Language::withoutCurrentLanguage()->where('not_visible','=', 0)->get();
                  
                  if (! empty($languages))
                  {
                      $this->add('translation', function ($model) use ($template, $route, $languages) 
                      {
                          $translations = Translation::byElement($model)->pluck('id_element','iso')->toArray();
                           return view($template, array(
                              'languages'    => $languages,
                              'translations' => $translations,
                              'data'         => $model->toArray(),
                              'route'        => !empty($route) ? $route . '@' : $this->getControllerNameForAction() . '@'
                          ))->render();
                      },
                      trans('user-management::datatable.translation'));
                  }
              }
          }

          public function addDefaultAction($template = 'user-management::user.form.components.datatable.actions', $route = '')
          {
              parent::addDefaultAction($template, $route);
          }
      } 