<?php namespace Distilleries\Expendable\Http\Controllers\Admin\Base;

      use Distilleries\DatatableBuilder\Contracts\DatatableStateContract;
      use Distilleries\Expendable\Contracts\ExportStateContract;
      use Distilleries\Expendable\Contracts\ImportStateContract;
      use Distilleries\Expendable\States\DatatableStateTrait;
      use Distilleries\Expendable\States\ExportStateTrait;
      use Distilleries\Expendable\States\ImportStateTrait;

      class FormLessComponent extends ModelBaseController implements DatatableStateContract, ExportStateContract, ImportStateContract 
      {
          use ExportStateTrait;
          use DatatableStateTrait;
          use ImportStateTrait;

          public function getIndex()
          {
              return $this->getIndexDatatable();
          }
      }