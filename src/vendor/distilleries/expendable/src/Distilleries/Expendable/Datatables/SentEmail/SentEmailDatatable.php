<?php namespace Distilleries\Expendable\Datatables\SentEmail;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class SentEmailDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('email_type', null, trans('expendable::datatable.email_type_id'));

              $this->add('created_at', function($model)
              {
                    return FormUtils::diffForHumans($model->created_at);
              },trans('expendable::datatable.sent_date'));

              $this->add('sent_to_email', null, trans('expendable::datatable.sent_to_email'));
              $this->add('request_ip_address', null, trans('expendable::datatable.request_ip_address'));
              $this->add('request_session', null, trans('expendable::datatable.request_session'));
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