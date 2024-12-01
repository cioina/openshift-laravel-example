<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class UsState extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'state_code',
              'state_name',
              'is_territory',
          ];
      }