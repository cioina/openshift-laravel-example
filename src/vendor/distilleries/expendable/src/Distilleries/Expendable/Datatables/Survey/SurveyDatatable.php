<?php namespace Distilleries\Expendable\Datatables\Survey;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class SurveyDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('client_id', null, trans('expendable::datatable.client_id'));
              $this->add('updated_at', function($model)
              {
                    return FormUtils::diffForHumans($model->created_at);
              },trans('expendable::datatable.updated_at'));
              $this->add('person_name', null, trans('expendable::datatable.person_name'));
              $this->add('is_complex_name', null, trans('expendable::datatable.is_complex_name'));
              $this->add('complex_name', null, trans('expendable::datatable.complex_name'));
              $this->add('email', null, trans('expendable::datatable.email_address'));
              $this->add('gender', null, trans('expendable::datatable.gender'));
              $this->add('phone', null, trans('expendable::datatable.phone'));
              $this->add('state_code', null, trans('expendable::datatable.state_code'));
              $this->add('zip', null, trans('expendable::datatable.zip'));
              $this->add('country_code', null, trans('expendable::datatable.country_code'));
              $this->add('first_drive_age', null, trans('expendable::datatable.age'));
              $this->add('ip_address', null, trans('expendable::datatable.request_ip_address'));
              $this->add('session_id', null, trans('expendable::datatable.request_session'));
              $this->add('recent_vacation', null, trans('expendable::datatable.vacation'));
              $this->add('birthday', null, trans('expendable::datatable.birthday'));
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