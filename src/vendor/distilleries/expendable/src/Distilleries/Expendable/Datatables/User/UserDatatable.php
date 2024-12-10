<?php namespace Distilleries\Expendable\Datatables\User;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      class UserDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[1, 'asc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('email', null, trans('expendable::datatable.email'));
              $this->addDefaultAction();
          }

          public function applyFilters()
          {
              parent::applyFilters();

              $status = Request::get('status', null);
              if (isset($status) && $status === '0')
              {
                  $this->model = $this->model->where('status', '=', 0);
              }

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 1){
                      Request::merge([
                          'iSortCol_0' => 0, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }

          public function filters()
          {
              $this->form->add('status', 'choice', [
                  'choices'     => StaticLabel::status(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.status')
              ]);
          }
      }