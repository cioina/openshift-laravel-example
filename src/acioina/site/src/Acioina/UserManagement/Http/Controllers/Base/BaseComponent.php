<?php namespace Acioina\UserManagement\Http\Controllers\Base;

      use Acioina\UserManagement\Contracts\DatatableStateContract;
      use Acioina\UserManagement\States\DatatableStateTrait;
      use Acioina\UserManagement\States\FormStateTrait;

      class BaseComponent extends ModelBaseController implements  DatatableStateContract  {

          use FormStateTrait, DatatableStateTrait;

          public function getIndex()
          {
              return $this->getIndexDatatable();
          }
      }