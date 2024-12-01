<?php namespace Distilleries\Expendable\Datatables\Service;

use Distilleries\Expendable\Datatables\BaseDatatable;
use Illuminate\Support\Facades\Request;

class ServiceDatatable extends BaseDatatable {

    public function build()
    {
        $this->defaultOrder = [[1, 'asc']];
        $this
            ->add('id', null, trans('expendable::datatable.id'))
            ->add('action', null, trans('expendable::datatable.action'));

        $this->addDefaultAction();
    }

    public function applyFilters()
    {
        parent::applyFilters();

        $iSortCol = Request::get('iSortCol_0', null);
        if(!is_null($iSortCol) && is_numeric($iSortCol))
        {
            if ( $iSortCol > 1){
                Request::merge([
                    'iSortCol_0' => 0, 
                    'sSortDir_0' => 'desc'
                    ]);
            }
        }
    }
}