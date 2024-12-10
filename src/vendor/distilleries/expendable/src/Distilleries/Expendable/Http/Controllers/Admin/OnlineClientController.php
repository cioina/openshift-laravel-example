<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\OnlineClient\OnlineClientDatatable;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\FormLessComponent;
      use Distilleries\Expendable\Models\OnlineClient;

      class OnlineClientController extends FormLessComponent {

          public function __construct(OnlineClientDatatable $datatable, OnlineClient $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
          }
      }