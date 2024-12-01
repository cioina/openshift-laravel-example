<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Service extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'id', 
              'action'
          ];

          public function permissions()
          {
              return $this->hasMany('Distilleries\Expendable\Models\Permission');
          }

          public function getByAction($action) {
              return $this->where('action', '=', $action)->get();
          }
      }