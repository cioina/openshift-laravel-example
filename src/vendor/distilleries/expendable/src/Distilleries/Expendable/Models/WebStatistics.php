<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class WebStatistics extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $table = 'web_statistics';

          protected $fillable = [
              'browser_id',
          ];
      }