<?php namespace Acioina\UserManagement\Http\Datatables\WebPage;

      use Acioina\UserManagement\Http\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      class WebPageDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[1, 'desc']];
              $this->add('label', null, trans('user-management::datatable.title'));
              $this->add('created_at', function($model)
              {
                  return FormUtils::diffForHumans($model->created_at);
              },trans('user-management::datatable.created_at'));
              $this->addDefaultAction();
              $this->addTranslationAction();
          }
          
          public function applyFilters()
          {
              parent::applyFilters();
              
              $sSearch = Request::get('sSearch', null);
              if(! is_null($sSearch))
              {
                  Request::merge([
                      'sSearch_0' => $sSearch
                      ]);
              }
              
              $iSortCol = Request::get('iSortCol_0', null);
              if(! is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 1 ){
                      Request::merge([
                          'iSortCol_0' => 1, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }

              $this->model = $this->model->where('status', '=', StaticLabel::STATUS_ONLINE);
          }

      }