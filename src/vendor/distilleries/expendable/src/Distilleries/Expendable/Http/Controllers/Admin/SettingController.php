<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\Setting\SettingDatatable;
      use Distilleries\Expendable\Forms\Setting\SettingForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\Setting;

      class SettingController extends BaseComponent 
      {

          public function __construct(SettingDatatable $datatable, SettingForm $form, Setting $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }