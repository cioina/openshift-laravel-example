<?php namespace Distilleries\Expendable\Forms\VideoType;

      use Distilleries\FormBuilder\FormValidator;

      class VideoTypeForm extends FormValidator {

          public static $rules = [
              'video_type_name'   => 'required',
              'video_type_id'     => 'required|integer'
          ];

          public function buildForm()
          {
              $this
                  ->add($this->model->getKeyName(), 'hidden')
                  ->add('video_type_id', 'text', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.email_type_id')
                ])->add('video_type_name', 'text', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.email_type_name')
                ]) ->addDefaultActions(); 
          }
      }