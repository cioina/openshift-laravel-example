<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Role extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'label',
              'initials',
              'overide_permission'
          ];

          public function user()
          {
              return $this->hasOne('Distilleries\Expendable\Models\User');
          }

          public function permissions()
          {
              return $this->hasMany('Distilleries\Expendable\Models\Permission');
          }
      }