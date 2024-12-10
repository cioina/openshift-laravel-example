<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\Country\CountryDatatable;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\FormLessComponent;
      use Distilleries\Expendable\Models\Country;

      class CountryController extends FormLessComponent 
      {

          public function __construct(CountryDatatable $datatable, Country $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
          }
      }