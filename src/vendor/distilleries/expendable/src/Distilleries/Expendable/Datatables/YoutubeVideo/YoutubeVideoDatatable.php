<?php namespace Distilleries\Expendable\Datatables\YoutubeVideo;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Models\YoutubeVideo;

      use Illuminate\Support\Facades\Request;

      class YoutubeVideoDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[3, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('small_image_url', function($model){
                  return FormUtils::getImage( $model->small_image_url,
                      $model->id,
                      $model->label
                      ,'img_group1',
                      $model->original_image_url);
              },
              trans('expendable::datatable.image'));
              
              $this->add('label', null, trans('expendable::datatable.title'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));
              $this->add('video_type', null, trans('expendable::datatable.video_type'));
              $this->addDefaultAction();
          }
          
          public function applyFilters()
          {
              parent::applyFilters();

              $status = Request::get('status', null);
              if (isset($status) && $status === '0')
              {
                  $this->model = $this->model->where('status', '=', 0);
              }

              $status = Request::get('video_type', null);
              if (isset($status) && $status != '')
              {
                  $this->model = $this->model->where('video_type', '=', $status);
              }

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 4 || $iSortCol == 1){
                      Request::merge([
                          'iSortCol_0' => 3, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }

          public function filters()
          {
              $this->form->add('status', 'choice', [
                  'choices'     => StaticLabel::status(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.status')
              ]);

              $this->form->add('video_type', 'choice', [
                  'choices'     => StaticLabel::videoType(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.video_type')
              ]);

          }

      }