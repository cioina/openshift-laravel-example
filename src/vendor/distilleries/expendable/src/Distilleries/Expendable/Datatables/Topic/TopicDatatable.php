<?php namespace Distilleries\Expendable\Datatables\Topic;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      class TopicDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('label', null, trans('expendable::datatable.title'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));
              $this->addTranslationAction();
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
                  if ( $iSortCol > 2 ){
                      Request::merge([
                          'iSortCol_0' => 2, 
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