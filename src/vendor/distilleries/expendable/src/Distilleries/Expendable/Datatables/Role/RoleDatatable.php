<?php namespace Distilleries\Expendable\Datatables\Role;

use Distilleries\Expendable\Datatables\BaseDatatable;
use Illuminate\Support\Facades\Request;

class RoleDatatable extends BaseDatatable
{
    public function build()
    {
        $this->defaultOrder = [[1, 'asc']];
        $this
            ->add('id', null, trans('expendable::datatable.id'))
            ->add('label', null, trans('expendable::datatable.title'))
            ->add('initials', null, trans('expendable::datatable.initials'));

        $this->addDefaultAction();

    }

    public function applyFilters()
    {
        parent::applyFilters();

        $iSortCol = Request::get('iSortCol_0', null);
        if(!is_null($iSortCol) && is_numeric($iSortCol))
        {
            if ( $iSortCol > 2){
                Request::merge([
                    'iSortCol_0' => 0, 
                    'sSortDir_0' => 'desc'
                    ]);
            }
        }
    }

}