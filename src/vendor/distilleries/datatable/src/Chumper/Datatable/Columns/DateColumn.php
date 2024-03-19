<?php namespace Chumper\Datatable\Columns;

      //use Carbon\Carbon;
      class DateColumn extends BaseColumn {

          /**
           * Constants for the time representation
           */
          const DATE = 0;
          const TIME = 1;
          const DATE_TIME = 2;
          const CUSTOM = 4;
          const FORMATTED_DATE = 5;
          const DAY_DATE = 6;

          /**
           * @var int The format to show
           */
          private $format;

          /**
           * @var string custom show string if chosen
           */
          private $custom;

          function __construct($name, $format = 2, $custom = "")
          {
              parent::__construct($name);
              $this->format = $format;
              $this->custom = $custom;
          }

          /**
           * @param mixed $model The data to pass to the column,
           *              could be a model or an array
           * @return mixed the return value of the implementation,
           *              should be text in most of the cases
           */
          public function run($model)
          {

              if(is_string(is_array($model) ? $model[$this->name]: $model->{$this->name}))
              {
                  return is_array($model) ? $model[$this->name]: $model->{$this->name};
              }

              switch($this->format)
              {
                  //case DateColumn::DATE:
                  //    return is_array($model) ? $model[$this->name]->toDateString(): $model->{$this->name}->toDateString();
                  //    break;
                  //case DateColumn::TIME:
                  //    return is_array($model) ? $model[$this->name]->toTimeString(): $model->{$this->name}->toTimeString();
                  //    break;
                  //case DateColumn::DATE_TIME:
                  //    return is_array($model) ? $model[$this->name]->toDateTimeString(): $model->{$this->name}->toDateTimeString();
                  //    break;
                  //case DateColumn::CUSTOM:
                  //    return is_array($model) ? $model[$this->name]->format($this->custom): $model->{$this->name}->format($this->custom);
                  //    break;
                  //case DateColumn::FORMATTED_DATE:
                  //    return is_array($model) ? $model[$this->name]->toFormattedDateString(): $model->{$this->name}->toFormattedDateString();
                  //    break;
                  
                  case DateColumn::DAY_DATE:
                      //To test null value
                      //return is_array($model) ? (isset($model[$this->name])?$model[$this->name]->format('Y-m-d H:i:s'):' '): (isset($model->{$this->name})?$model->{$this->name}->format('Y-m-d H:i:s'):' '); 

                      //return is_array($model) ? Carbon::parse($model[$this->name])->format('l jS \of F Y h:i:s A'): Carbon::parse($model->{$this->name})->format('l jS \of F Y h:i:s A');
                      //return is_array($model) ? Carbon::parse($model[$this->name])->format('Y-m-d H:i:s'): Carbon::parse($model->{$this->name})->format('Y-m-d H:i:s');
                      return is_array($model) ? $model[$this->name]->format('Y-m-d H:i:s'): $model->{$this->name}->format('Y-m-d H:i:s'); 
                      break;

              }
          }
      }