<?php namespace Distilleries\Expendable\Datatables\CodeBlock;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      class CodeBlockDatatable extends BaseDatatable 
      {
          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('code_type', function($model)
              {
                  return StaticLabel::codeType($model->code_type);
              },
              trans('expendable::datatable.type'));
              
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));  
              $this->addDefaultAction();
              
              $this->add('code_block', function($model)
              {
                  return $model->label . FormUtils::getCodeBlock( $model->code_block, $model->code_type) . $model->content;
              },
              trans('expendable::datatable.content'));
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