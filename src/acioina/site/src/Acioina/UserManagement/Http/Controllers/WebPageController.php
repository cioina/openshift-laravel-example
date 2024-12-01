<?php namespace Acioina\UserManagement\Http\Controllers;

use Acioina\UserManagement\Contracts\LayoutManagerContract;
use Acioina\UserManagement\Http\Datatables\WebPage\WebPageDatatable;
use Acioina\UserManagement\Http\Forms\WebPage\WebPageForm;
use Acioina\UserManagement\Http\Controllers\Base\BaseComponent;
use Distilleries\Expendable\Models\WebPage;

class WebPageController extends BaseComponent 
{
    // These are "global" keys
    const KEY_SESSION_LOGOUT_URL = 'fb_logoutUrl';
    const KEY_SESSION_FB_TOKEN   = 'fb_token';
    const KEY_SESSION_FB_PICTURE = 'fb_picture';

    public function __construct(WebPageDatatable $datatable, WebPageForm $form, WebPage $model, LayoutManagerContract $layoutManager)
    {
        parent::__construct($model, $layoutManager);

        if (isset($_SESSION[self::KEY_SESSION_LOGOUT_URL]) )
        {
            unset($_SESSION[self::KEY_SESSION_LOGOUT_URL]);
            unset($_SESSION[self::KEY_SESSION_FB_TOKEN]);
            unset($_SESSION[self::KEY_SESSION_FB_PICTURE]);
        }

        $this->datatable = $datatable;
        $this->form      = $form;
    }

}