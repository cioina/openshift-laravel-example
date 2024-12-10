<?php namespace Distilleries\Expendable\Datatables\UsState;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class UsStateDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[0, 'asc']];
              $this->add('state_code', null, trans('expendable::datatable.state_code'));
              $this->add('state_name', null, trans('expendable::datatable.state_name'));
              $this->add('is_territory', null, trans('expendable::datatable.is_territory'));
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 2){
                      Request::merge([
                          'iSortCol_0' => 0, 
                          'sSortDir_0' => 'asc'
                          ]);
                  }
              }
          }
      }