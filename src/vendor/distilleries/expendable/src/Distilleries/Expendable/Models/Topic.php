<?php namespace Distilleries\Expendable\Models;

      use Distilleries\Expendable\Scopes\Translatable;
      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Topic extends Model implements BaseModelContract {

          use BaseModel, Translatable;

          protected $fillable = [
              'label',
              'content',
              'status'
          ];
          
      }