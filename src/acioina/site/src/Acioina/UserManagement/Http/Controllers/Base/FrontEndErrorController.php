<?php namespace Acioina\UserManagement\Http\Controllers\Base;
use Exception;

class FrontEndErrorController extends FrontEndBaseController
{
     public function display(Exception $exception, $code)
    {
        return response()->make($this->layoutManager->render(), $code);
    }
} 