<?php namespace Distilleries\Expendable\Datatables\Setting;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;
      
      class SettingDatatable extends BaseDatatable 
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
              $this->addTranslationAction();          
              $this->add('code_block', function($model)
              {
                  return $model->label . FormUtils::getCodeBlock( $model->code_block, $model->code_type) . $model->content;
              },
              trans('expendable::datatable.content'));
          }
          
          public function applyFilters()
          {
              parent::applyFilters();
              
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
      }