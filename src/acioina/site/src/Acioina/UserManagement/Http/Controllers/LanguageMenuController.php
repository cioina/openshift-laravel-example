<?php namespace Acioina\UserManagement\Http\Controllers;

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
                  $languages = Language::withoutCurrentLanguage()->where('not_visible','=',0)->get();
                  foreach($languages as $language)
                  {
                      $iso = explode('_', $language->iso);
                      $iso = strtolower($iso[0]);
                      if( $iso === $locale )
                      {
                          app()->setLocale($locale);
                          Session::put(TranslationUtils::KEY_LOCALE, $locale);
                          TranslationUtils::incAdminServiceProvaderCounter();

                          $redirect = TranslationUtils::getRedirectBack();
                          if ($redirect !== false)
                          {
                              TranslationUtils::forgetRedirectBack();
                              return redirect()->to(action($redirect['controller'],[$redirect['id']]), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
                              
                          }else{
                              return redirect()->to($GLOBALS['CIOINA_Config']->get('HomePage'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
                          }
                      }
                  }
              }
              
              return redirect()->back();
          }
      }