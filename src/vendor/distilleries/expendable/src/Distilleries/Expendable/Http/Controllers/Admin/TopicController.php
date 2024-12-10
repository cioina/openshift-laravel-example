<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\Topic\TopicDatatable;
      use Distilleries\Expendable\Forms\Topic\TopicForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\Topic;

      class TopicController extends BaseComponent 
      {

          public function __construct(TopicDatatable $datatable, TopicForm $form, Topic $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }