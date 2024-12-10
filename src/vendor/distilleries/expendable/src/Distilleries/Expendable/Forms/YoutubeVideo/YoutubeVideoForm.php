<?php namespace Distilleries\Expendable\Forms\YoutubeVideo;

      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\FormBuilder\FormValidator;

      class YoutubeVideoForm extends FormValidator {

          public static $rules = [
              'label'   => 'required',
              'status'    => 'required|integer'
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
                  ->add('video_id', 'text', [
                       'validation' => 'required',
                       'label'      => trans('expendable::form.video_id')
                  ])
                   ->add('small_image_quality', 'choice', [
                      'choices'     => StaticLabel::youtubeImageThumbnail(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.thumbnail_quality')
                  ])
                  ->add('original_image_quality', 'choice', [
                      'choices'     => StaticLabel::youtubeImageQuality(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.image_quality')
                  ])
                  ->add('domain', 'choice', [
                      'choices'     => StaticLabel::youtubeDomain(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.domain_start')
                  ])
                  ->add('video_type', 'choice', [
                      'choices'     => StaticLabel::videoType(),
                      'empty_value' => '-',
                      'validation'  => 'required',
                      'label'       => trans('expendable::form.video_type')
                  ])
                  ->add('start', 'text', [
                        'label'      => trans('expendable::form.start')
                  ])
                  ->add('end', 'text', [
                       'label'      => trans('expendable::form.end')
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
                  ->addDefaultActions();
          }
      }