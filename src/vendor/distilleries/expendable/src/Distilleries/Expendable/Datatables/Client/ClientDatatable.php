<?php namespace Distilleries\Expendable\Datatables\Client;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Support\Facades\Request;

      class ClientDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('fb_email', null, trans('expendable::datatable.fb_email'));
              $this->add('created_at', function($model)
              {
                    return FormUtils::diffForHumans($model->created_at);
              },trans('expendable::datatable.created_at'));
              $this->add('fb_first_name', null, trans('expendable::datatable.first_name'));
              $this->add('fb_last_name', null, trans('expendable::datatable.last_name'));
              $this->add('fb_verified', null, trans('expendable::datatable.fb_verified'));
              $this->add('phone', null, trans('expendable::datatable.phone'));
              $this->add('state_code', null, trans('expendable::datatable.state_code'));
              $this->add('zip', null, trans('expendable::datatable.zip'));
              $this->add('country_code', null, trans('expendable::datatable.country_code'));
              $this->add('gender', null, trans('expendable::datatable.gender'));
              $this->add('request_ip_address', null, trans('expendable::datatable.request_ip_address'));
              $this->add('request_id', null, trans('expendable::datatable.request_id'));
              $this->add('is_suspended', null, trans('expendable::datatable.is_suspended'));
              $this->add('is_deleted', null, trans('expendable::datatable.is_deleted'));
              $this->add('birthday', null, trans('expendable::datatable.birthday'));
              $this->addDefaultAction('expendable::admin.form.components.datatable.delete');
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 14){
                      Request::merge([
                          'iSortCol_0' => 2, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }
      }