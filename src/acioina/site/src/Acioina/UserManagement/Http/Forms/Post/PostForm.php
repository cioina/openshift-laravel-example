<?php namespace Acioina\UserManagement\Http\Forms\Post;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\FormBuilder\FormValidator;
      use Distilleries\Expendable\Models\Topic;
      use Distilleries\Expendable\Models\WebPage;
      use \Request;

      class PostForm extends FormValidator 
      {
          public static $rules = [];

          public function buildForm()
          {

              if(FormUtils::getClientLogin() === false)
              {
                  FormUtils::setRedirect(Request::url());
                  
                  //This does not work with translated blog posts
                  $loginModel = WebPage::withoutTranslation()->where('slug','=', FormUtils::LOGIN_SLUG)->first();
                  if(!isset($loginModel->web_page_settings) || empty($loginModel->web_page_settings))
                  {
                      abort(403);
                  }
                  foreach ($loginModel->web_page_settings as $pageImage)
                  {
                      if (!isset($pageImage->setting) || empty($pageImage->setting))
                      {
                          abort(403);
                      }
                  }

                  $this ->add('content', 'tinymce', [
                                      'no_label'   => true,
                                      'default_value' => FormUtils::renderWebPage($loginModel, 'user-management')
                                      ]);
              }else {

                  $this->add('label', 'text', [
                         'is_title'   => true,
                         // See protected function needsLabel from  
                         // kris\laravel-form-builder\src\Kris\LaravelFormBuilder\Fields\FormField.php
                         'no_label'   => true, 
                         'default_value'=> FormUtils::getTitle(  
                             $this->model->label, 
                             $this->model->created_at,
                             $this->model->updated_at,
                             'user-management::form.title_dates'),
                     ]);
                  
                  $this ->add('content', 'tinymce', [
                  'no_label'   => true,
                  'default_value' => FormUtils::renderPost($this->model,'user-management', config('user-management.blog_view_uri'))
                  ]);
              }

              $this->addDefaultActions();
          }
      }