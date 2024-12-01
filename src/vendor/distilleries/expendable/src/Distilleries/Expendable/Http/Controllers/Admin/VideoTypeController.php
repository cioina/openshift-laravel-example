<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\VideoType\VideoTypeDatatable;
      use Distilleries\Expendable\Forms\VideoType\VideoTypeForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\VideoType;

      class VideoTypeController extends BaseComponent 
      {

          public function __construct(VideoTypeDatatable $datatable, VideoTypeForm $form, VideoType $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }
      }