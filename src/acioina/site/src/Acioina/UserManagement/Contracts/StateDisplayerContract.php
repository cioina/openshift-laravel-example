<?php namespace Acioina\UserManagement\Contracts;

interface StateDisplayerContract {

    public function setState($state);

    public function setStates($states);

    public function getRenderStateMenu($template = '');
}