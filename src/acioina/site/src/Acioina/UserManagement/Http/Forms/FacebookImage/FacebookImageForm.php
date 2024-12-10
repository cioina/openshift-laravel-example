<?php namespace Acioina\UserManagement\Http\Forms\FacebookImage;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\FormBuilder\FormValidator;

      class FacebookImageForm extends FormValidator 
      {
          public function buildForm()
          {
              $this->add($this->model->getKeyName(), 'hidden')
                 ->add('label', 'text', [
                     'is_title'   => true,
                     'no_label'   => true,
                     'default_value'=> FormUtils::getTitle(  
                         $this->model->label, 
                         $this->model->created_at,
                         $this->model->updated_at,
                         'user-management::form.title_dates'),
                 ])
                 ->add('original_image_url', 'img', [
                       'no_label'     => true, 
                       'default_value'=> FormUtils::getImage( 
                           $this->model->original_image_url,
                           $this->model->id,
                           $this->model->label, 
                           'img_group1', 
                           $this->model->original_image_url,
                           'user-management::form.click_image'),
                  ])
                 ->add('content', 'tinymce', [
                     'no_label'   => true
                 ])
                 ->addDefaultActions();
          }
      }