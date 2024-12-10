<?php namespace Acioina\UserManagement\Http\Controllers;

use Acioina\UserManagement\Contracts\LayoutManagerContract;
use Acioina\UserManagement\Http\Datatables\Post\PostDatatable;
use Acioina\UserManagement\Http\Forms\Post\PostForm;
use Acioina\UserManagement\Http\Controllers\Base\BaseComponent;
use Distilleries\Expendable\Models\Post;

class PostController extends BaseComponent {

    public function __construct(PostDatatable $datatable, PostForm $form, Post $model, LayoutManagerContract $layoutManager)
    {
        parent::__construct($model, $layoutManager);

        $this->datatable = $datatable;
        $this->form      = $form;
    }

}