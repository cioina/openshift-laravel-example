<?php namespace Distilleries\Expendable\Datatables\GuestEmail;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class GuestEmailDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[3, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('is_facebook', null, trans('expendable::datatable.is_facebook'));
              $this->add('email', null, trans('expendable::datatable.email_address'));

              $this->add('created_at', function($model)
              {
                    return FormUtils::diffForHumans($model->created_at);
              },trans('expendable::datatable.sent_date'));

              $this->add('first_name', null, trans('expendable::datatable.first_name'));
              $this->add('last_name', null, trans('expendable::datatable.last_name'));
              $this->add('email_subject', null, trans('expendable::datatable.email_subject'));
              $this->add('email_body', null, trans('expendable::datatable.email_body'));
              $this->add('phone', null, trans('expendable::datatable.phone'));
              $this->add('zip', null, trans('expendable::datatable.zip'));
              $this->add('gender', null, trans('expendable::datatable.gender'));
              $this->add('country_code', null, trans('expendable::datatable.country_code'));
              $this->add('state_code', null, trans('expendable::datatable.state_code'));
              $this->add('age', null, trans('expendable::datatable.age'));
              $this->add('has_facebook', null, trans('expendable::datatable.has_facebook'));
              $this->add('request_ip_address', null, trans('expendable::datatable.request_ip_address'));
              $this->add('request_id', null, trans('expendable::datatable.request_id'));
              $this->addDefaultAction('expendable::admin.form.components.datatable.delete');
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 15){
                      Request::merge([
                          'iSortCol_0' => 2, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }
      }