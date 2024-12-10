<?php namespace Distilleries\Expendable\Forms\FacebookImage;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\FormBuilder\FormValidator;

      class FacebookImageForm extends FormValidator {

          public static $rules = [
              'label'   => 'required',
              'status'  => 'required|integer'
          ];

          public function buildForm()
          {
              $this
                  ->add($this->model->getKeyName(), 'hidden')
                  ->add('original_image_url', 'img', [
                       'no_label'   => true, 
                       'default_value'=> FormUtils::getImage( $this->model->original_image_url,
                       $this->model->id,
                       $this->model->label, 
                       'img_group1', 
                       $this->model->original_image_url,
                       'expendable::form.click_image')
                  ])
                  ->add('small_image_url', 'text', [
                       'default_value'   => $this->model->original_image_url,
                       'label'           => trans('expendable::form.url')
                  ])
                  ->add('label', 'text', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.subject')
                  ])
                  ->add('content', 'tinymce', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.content')
                  ])
                  ->add('status', 'choice', [
                      'choices'     => StaticLabel::status(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.status')
                  ])
                  ->add('is_expired', 'choice', [
                      'choices'     => StaticLabel::yesNo(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.is_expired')
                  ])
                  ->addDefaultActions();
          }
      }