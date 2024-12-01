<?php namespace Acioina\UserManagement\States;

use Acioina\UserManagement\Contracts\StateDisplayerContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;

class StateDisplayer implements StateDisplayerContract 
{
    protected $states = [];
    protected $class = '';
    protected $view;
    public $config;

    public function __construct(Factory $view, array $config)
    {
        $this->view   = $view;
        $this->config = $config;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->states[] = $state;
    }

    /**
     * @param string $states
     */
    public function setStates($states)
    {
        $this->states = $states;
    }

    public function getRenderStateMenu($template = 'user-management::user.form.state.menu')
    {
        return $this->view->make($template, [
            'states' => $this->getTableState(),
            'action' => '\\' . $this->class . '@'
        ]);
    }

    protected function getTableState()
    {
        $table  = [];
        $config = $this->config['state'];

        foreach ($this->states as $state)
        {
            if (in_array($state, array_keys($config)))
            {
                $table[] = $config[$state];
            }
        }

        $table = Arr::sort($table, function($value)
        {
            return $value['position'];
        });

        return $table;
    }
}