<?php namespace Acioina\UserManagement\Fondation;


use Distilleries\Expendable\ExpendableRoutingServiceProvider;
use Acioina\UserManagement\UserManagementServiceProvider;
use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        parent::registerBaseServiceProviders();
        $this->register(new UserManagementServiceProvider($this));
        $this->register(new ExpendableRoutingServiceProvider($this));
    }


    /**
     * Override default application storage path.
     *
     * @return mixed
     */
    public function storagePath()
    {
        return  $this->basePath . DIRECTORY_SEPARATOR . $GLOBALS['CIOINA_Config']->get('LaravelStorage');
    }
}