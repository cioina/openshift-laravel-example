<?php namespace Distilleries\Expendable\Datatables\WebStatistics;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;
      use Distilleries\Expendable\Models\WebStatistics;

      class WebStatisticsDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[1, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));  
              $this->add('browser_id', null, trans('expendable::datatable.browser_id'));
              $this->add('request_ip_address', null, trans('expendable::datatable.request_ip_address'));
              $this->add('request_session', null, trans('expendable::datatable.request_session'));
              $this->add('is_fake_visitor', null, trans('expendable::datatable.is_fake_visitor'));
              
              $this->addDefaultAction();
              $this->add('absolute_uri', null, trans('expendable::datatable.absolute_uri'));
          }
          
          public function applyFilters()
          {
              parent::applyFilters();

              $status = Request::get('status', null);
              if (! empty($status))
              {
                  $this->model = $this->search($this->model,'request_ip_address', 'browser_id', 1);
              }

              $status = Request::get('fake', null);
              if (! empty($status))
              {
                  $this->model = $this->model->where('is_fake_visitor', '!=', 1);
              }

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 5 )
                  {
                      Request::merge([
                          'iSortCol_0' => 1, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }

          public function filters()
          {
              $this->form->add('status', 'choice', [
                  'choices'     => StaticLabel::yesNo(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.browser_id')
              ]);

              $this->form->add('fake', 'choice', [
                  'choices'     => StaticLabel::yesNo(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.is_fake_visitor')
              ]);

          }

          private function search($query, $searchQuery, $searchFiel, $searchValue, $isEqual = true)
          {
              return $query->where(function($query) use ($searchQuery, $searchFiel, $searchValue, $isEqual)
              {
                  $rows = WebStatistics::distinct()->select($searchQuery)->where($searchFiel, ($isEqual ? '=' : '!='), $searchValue)->groupBy($searchQuery)->get();

                  foreach ($rows as $row)
                  {
                      $query->where($searchQuery, '!=', $row->{$searchQuery});
                  }
              });
          }
       }