<?php namespace Distilleries\Expendable\Datatables\FacebookImage;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      class FacebookImageDatatable extends BaseDatatable 
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
              $this->add('is_expired', null, trans('expendable::datatable.is_expired'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));
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

              $status = Request::get('is_expired', null);
              if (isset($status) && $status != '')
              {
                  $this->model = $this->model->where('is_expired', '=', $status);
              }

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 4 || $iSortCol == 1){
                      Request::merge([
                          'iSortCol_0' => 4, 
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

              $this->form->add('is_expired', 'choice', [
                  'choices'     => StaticLabel::yesNo(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.is_expired')
              ]);

          }

      }