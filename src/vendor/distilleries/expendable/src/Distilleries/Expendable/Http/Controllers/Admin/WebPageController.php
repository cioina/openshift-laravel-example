<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\WebPage\WebPageDatatable;
      use Distilleries\Expendable\Forms\WebPage\WebPageForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\WebPage;

      class WebPageController extends BaseComponent 
      {

          public function __construct(WebPageDatatable $datatable, WebPageForm $form, WebPage $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }