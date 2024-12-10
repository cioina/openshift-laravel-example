<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\SentEmail\SentEmailDatatable;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\FormLessComponent;
      use Distilleries\Expendable\Models\SentEmail;

      class SentEmailController extends FormLessComponent 
      {

          public function __construct(SentEmailDatatable $datatable, SentEmail $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
          }
      }