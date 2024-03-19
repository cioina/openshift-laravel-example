<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class OnlineClient extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [];
      }
