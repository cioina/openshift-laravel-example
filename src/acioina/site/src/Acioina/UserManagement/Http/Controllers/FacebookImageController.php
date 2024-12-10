<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Contracts\LayoutManagerContract;
      use Acioina\UserManagement\Http\Datatables\FacebookImage\FacebookImageDatatable;
      use Acioina\UserManagement\Http\Forms\FacebookImage\FacebookImageForm;
      use Acioina\UserManagement\Http\Controllers\Base\BaseComponent;
      use Distilleries\Expendable\Models\FacebookImage;

      class FacebookImageController extends BaseComponent {

          public function __construct(FacebookImageDatatable $datatable, FacebookImageForm $form, FacebookImage $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }