<?php namespace Distilleries\Expendable\Events;

use \Event;
use Distilleries\Expendable\Contracts\EventContract;

class EventDispatcher implements EventContract 
{
    protected $event_name;
    protected $params = [];

    public function __construct($eventName, $params = [], $auto_fire = true)
    {
        $this->event_name = $eventName;
        $this->params     = $params;

        if ($auto_fire === true)
        {
            $this->fire($this->params);
        }
    }

    public function fire($params = [])
    {
        Event::dispatch($this->event_name, [$params,]);
    }
}