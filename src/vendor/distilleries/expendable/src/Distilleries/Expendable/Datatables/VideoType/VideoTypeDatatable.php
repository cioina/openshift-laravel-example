<?php namespace Distilleries\Expendable\Datatables\VideoType;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Illuminate\Support\Facades\Request;

      class VideoTypeDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[1, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at')); 
              $this->add('video_type_id', null, trans('expendable::datatable.email_type_id'));
              $this->add('video_type_name', null, trans('expendable::datatable.email_type_name'));
              
              $this->addDefaultAction();
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 3){
                      Request::merge([
                          'iSortCol_0' => 0, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }
      }