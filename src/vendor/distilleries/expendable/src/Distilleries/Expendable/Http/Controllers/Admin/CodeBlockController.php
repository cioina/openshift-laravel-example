<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\CodeBlock\CodeBlockDatatable;
      use Distilleries\Expendable\Forms\CodeBlock\CodeBlockForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\CodeBlock;

      class CodeBlockController extends BaseComponent 
      {

          public function __construct(CodeBlockDatatable $datatable, CodeBlockForm $form, CodeBlock $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }