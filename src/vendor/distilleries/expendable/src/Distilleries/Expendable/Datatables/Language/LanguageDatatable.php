<?php namespace Distilleries\Expendable\Datatables\Language;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Illuminate\Support\Facades\Request;

      class LanguageDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this
                  ->add('id', null, trans('expendable::datatable.id'))
                  ->add('label', null, trans('expendable::datatable.label'))
                  ->add('iso', null, trans('expendable::datatable.iso'));

              $this->addDefaultAction();

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
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }

          public function setClassRow($datatable)
          {
              $datatable->setRowClass(function($row)
              {
                  $class = (isset($row->status) && empty($row->status)) ? 'danger' : '';
                  $class = (empty($class) && !empty($row->not_visible)) ? 'warning' : $class;

                  return $class;
              });

              return $datatable;
          }
      }