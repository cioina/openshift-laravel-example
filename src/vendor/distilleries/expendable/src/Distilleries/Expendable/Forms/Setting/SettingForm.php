<?php namespace Distilleries\Expendable\Forms\Setting;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\FormBuilder\FormValidator;

      class SettingForm extends FormValidator {

          public static $rules = [
              'label'   => 'required',
              'code_type' => 'required',
          ];

          public function buildForm()
          {
              $this
                  ->add($this->model->getKeyName(), 'hidden')
                  ->add('label', 'text', [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.subject')
                  ])
                  ->add('code_type', 'choice', [
                      'choices'     => StaticLabel::codeType(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.code_type')
                  ]);
              
              $action = explode('@', \Route::currentRouteAction());
              if($action[1] === "getView")
              {
                  $this->add('code_block', 'tinymce', [
                  'validation' => 'required',
                  'label'      => trans('expendable::form.code_block'),
                  'default_value'=> FormUtils::getCodeBlock( $this->model->code_block, $this->model->code_type)
                  ]);

              }else{
                  $this->add('code_block', 'code_block', [
                        'validation' => 'required',
                        'label'      => trans('expendable::form.code_block')
                        ]);
              }
              
              $this
               ->add('content', 'tinymce', [
                   'validation' => 'required',
                   'label'      => trans('expendable::form.content')
               ])
               ->addDefaultActions();
          }
      }