<?php namespace Distilleries\Expendable\Datatables\OnlineClient;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class OnlineClientDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('client_id', null, trans('expendable::datatable.client_id'));
              $this->add('updated_at', function($model)
              {
                    return FormUtils::diffForHumans($model->updated_at);
              },trans('expendable::datatable.updated_at'));
              $this->add('is_logged_out', null, trans('expendable::datatable.is_logged_out'));
              $this->add('ip_address', null, trans('expendable::datatable.request_ip_address'));
              $this->add('session_id', null, trans('expendable::datatable.request_session'));
              $this->addDefaultAction('expendable::admin.form.components.datatable.delete');
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 5){
                      Request::merge([
                          'iSortCol_0' => 2, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }
      }