<?php namespace Distilleries\Expendable\Forms\EmailType;

      use Distilleries\FormBuilder\FormValidator;

      class EmailTypeForm extends FormValidator {

          public static $rules = [
              'email_type_name'   => 'required',
              'email_type_id'     => 'required|integer'
          ];

          public function buildForm()
          {
              $this
                  ->add($this->model->getKeyName(), 'hidden')
                  ->add('email_type_id', 'text', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.email_type_id')
                ])->add('email_type_name', 'text', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.email_type_name')
                ]) ->addDefaultActions(); 
          }
      }