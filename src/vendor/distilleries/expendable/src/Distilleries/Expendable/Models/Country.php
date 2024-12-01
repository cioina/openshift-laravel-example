<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Country extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $table = 'countries';

          protected $fillable = [
              'country_code',
              'country_name',
          ];
      }