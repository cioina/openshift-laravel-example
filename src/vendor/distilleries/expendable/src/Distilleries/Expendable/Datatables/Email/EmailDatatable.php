<?php namespace Distilleries\Expendable\Datatables\Email;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      class EmailDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[3, 'asc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('label', null, trans('expendable::datatable.subject'));
              $this->add('body_type', function($model)
              {
                  return StaticLabel::bodyType($model->body_type);
              },
              trans('expendable::datatable.type'));
              
              $this->add('action', function($model)
              {
                  return StaticLabel::mailActions($model->action);
              },
              trans('expendable::datatable.action'));
              
              $this->add('cc');
              $this->add('bcc');
              $this->addTranslationAction();
              $this->addDefaultAction();
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 5){
                      Request::merge([
                          'iSortCol_0' => 0, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }

      }