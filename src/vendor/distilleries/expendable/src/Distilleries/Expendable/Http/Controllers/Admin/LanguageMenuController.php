<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Illuminate\Routing\Controller as BaseController;
      use Distilleries\Expendable\Models\Language;
      use Distilleries\Expendable\Helpers\TranslationUtils;
      use Session;

      class LanguageMenuController extends BaseController
      {
          public function getIndex($locale = null)
          {
              if(! empty($locale))
              {
                  $languages = Language::withoutCurrentLanguage()->get();
                  foreach($languages as $language)
                  {
                      $iso = explode('_', $language->iso);
                      $iso = strtolower($iso[0]);
                      if( $iso === $locale )
                      {
                          app()->setLocale($locale);
                          Session::put(TranslationUtils::KEY_LOCALE, $locale);
                          break; 
                      }
                  }
              }
              
              return redirect()->back();
          }
      }