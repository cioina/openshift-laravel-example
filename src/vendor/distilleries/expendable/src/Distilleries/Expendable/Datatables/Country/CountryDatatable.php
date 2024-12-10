<?php namespace Distilleries\Expendable\Datatables\Country;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class CountryDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[0, 'asc']];
              $this->add('country_code', null, trans('expendable::datatable.country_code'));
              $this->add('country_name', null, trans('expendable::datatable.country_name'));
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 1){
                      Request::merge([
                          'iSortCol_0' => 0, 
                          'sSortDir_0' => 'asc'
                          ]);
                  }
              }
          }
      }