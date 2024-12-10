<?php namespace Distilleries\Expendable\Forms\Topic;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\FormBuilder\FormValidator;

      class TopicForm extends FormValidator 
      {
          public static $rules = [
              'label'   => 'required',
              'status'  => 'required|integer'
          ];

          public function buildForm()
          {
              $this
                  ->add($this->model->getKeyName(), 'hidden')
                  ->add('label', 'text', 
                  [
                       'validation' => 'required',
                       'label'      => trans('expendable::form.subject')
                  ])
                  ->add('content', 'tinymce', 
                  [
                       'validation' => 'required',
                       'label'      => trans('expendable::form.content')
                  ])
                  ->add('status', 'choice', 
                  [
                       'choices'     => StaticLabel::status(),
                       'empty_value' => '-',
                       'validation'  => 'required',
                       'label'       => trans('expendable::form.status')
                  ])
                  ->addDefaultActions();
          }
      }