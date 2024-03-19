<?php namespace Distilleries\PermissionUtil\Helpers;

use Distilleries\PermissionUtil\Contracts\PermissionUtilContract;
use Illuminate\Contracts\Auth\Guard;

class PermissionUtil implements PermissionUtilContract {

    protected $auth;
    protected $config;

    public function __construct(Guard $auth) {
        $this->auth   = $auth;
    }

    public function hasAccess($key)
    {
        if ($this->auth->check()) 
        {
            $user = $this->auth->user();
            $implement = class_implements($user, true);

            if (empty($implement) || empty($implement['Distilleries\PermissionUtil\Contracts\PermissionUtilContract'])) 
            {
                return false;
            }

            return (!empty($user)) ? $user->hasAccess($key) : false;
        }

        return false;
    }
}