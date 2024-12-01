<?php namespace Distilleries\Expendable\Forms\WebPage;

      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\FormBuilder\FormValidator;
      use Illuminate\Support\Collection;

      class WebPageForm extends FormValidator 
      {
          public static $rules = [
             'label'   => 'required',
             'slug'    => 'required',
             'status'  => 'required|integer'
         ];

          public function buildForm()
          {
              $this
              ->add($this->model->getKeyName(), 'hidden')
              ->add('label', 'text', [
                  'validation' => 'required',
                  'label'      => trans('expendable::form.subject')
              ])
              ->add('slug', 'text', [
                  'validation' => 'required',
                  'label'      => trans('expendable::form.slug')
              ]);

              $action = explode('@', \Route::currentRouteAction());
              if($action[1] === "getView")
              {
                  $this ->add('content', 'tinymce', [
                  'label'      => trans('expendable::form.content'),
                  'default_value'=> FormUtils::renderWebPage($this->model,'expendable')
                  ]);
              }elseif(!$this->model->is_raw){
                  $this ->add('content', 'tinymce', 
                  [
                      'validation' => 'required',
                      'label'      => trans('expendable::form.content')
                  ]);
              } else{
                  $this->add('content', 'code_block', [
                        'validation' => 'required',
                        'label'      => trans('expendable::form.content')
                        ]);
              }
              
              $this
                  ->add('is_public', 'choice', [
                      'choices'     => StaticLabel::yesNo(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.is_public')
                  ])
                  ->add('has_form', 'choice', [
                      'choices'     => StaticLabel::yesNo(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.has_form')
                  ])
                  ->add('status', 'choice', [
                      'choices'     => StaticLabel::status(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.status')
                  ])
                  ->add('is_raw', 'choice', [
                      'choices'     => StaticLabel::yesNo(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.is_raw_html')
                  ])
                  ->addDefaultActions();
          }
      }