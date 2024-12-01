<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\UsState\UsStateDatatable;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\FormLessComponent;
      use Distilleries\Expendable\Models\UsState;

      class UsStateController extends FormLessComponent 
      {

          public function __construct(UsStateDatatable $datatable, UsState $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
          }
      }