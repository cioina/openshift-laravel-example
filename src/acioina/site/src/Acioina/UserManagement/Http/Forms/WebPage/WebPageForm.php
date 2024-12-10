<?php namespace Acioina\UserManagement\Http\Forms\WebPage;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\FormBuilder\FormValidator;
      use Distilleries\Expendable\Models\WebPage;
      use Illuminate\Support\Collection;
      use \Request;

      class WebPageForm extends FormValidator 
      {
          public static $rules = [];

          public function buildForm()
          {
              $isLoggedIn = FormUtils::getClientLogin();

              if(! $this->model->is_public && $isLoggedIn === false && $this->model->slug !== FormUtils::LOGIN_SLUG)
              {
                  FormUtils::setRedirect(Request::url());

                  $this ->add('content', 'tinymce', [
                                      'no_label'   => true,
                                      'default_value' => FormUtils::renderWebPage(
                                      WebPage::withoutTranslation()->where('slug','=', FormUtils::LOGIN_SLUG)->first(),'user-management')
                                      ]);
              }else {

                  if($isLoggedIn !== false && $this->model->slug === FormUtils::LOGIN_SLUG)
                  {
                      FormUtils::setMessages([trans('user-management::form.client_login')]);
                  }
                  
                  $this ->add('content', 'tinymce', [
                  'no_label'   => true,
                  'default_value' => FormUtils::renderWebPage($this->model,'user-management')
                  ]);
              }

              $this->addDefaultActions();
          }
      }