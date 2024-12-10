<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\EmailType\EmailTypeDatatable;
      use Distilleries\Expendable\Forms\EmailType\EmailTypeForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\EmailType;

      class EmailTypeController extends BaseComponent 
      {

          public function __construct(EmailTypeDatatable $datatable, EmailTypeForm $form, EmailType $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }