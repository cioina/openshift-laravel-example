<?php namespace Distilleries\Expendable\Forms\Post;

      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\FormBuilder\FormValidator;

      class PostForm extends FormValidator {

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
                  'label'      => trans('expendable::form.subject')])
              ->add('slug', 'text', [
                  'validation' => 'required',
                  'label'      => trans('expendable::form.slug')])
              ->add('description', 'code_block', [
                        'validation' => 'required',
                        'label'      => trans('expendable::form.description')])
              ->add('facebook_image_id', 'text', [
              'validation' => 'required',
              'label'      => trans('expendable::form.image_id')
              ]);

              $action = explode('@', \Route::currentRouteAction());
              if($action[1] === "getView")
              {
                  $this ->add('content', 'tinymce',
                  [
                      'label'         => trans('expendable::form.content'),
                      'default_value' => FormUtils::renderPost($this->model, 'expendable', '/'. config('expendable.admin_base_uri') . config('expendable.blog_view_uri'))
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
                  ->add('status', 'choice',
                  [
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