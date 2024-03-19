<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;
      use Distilleries\Expendable\Models\StatusTrait;

      class Language extends Model implements BaseModelContract 
      {
          use BaseModel, StatusTrait;

          protected $fillable = [
              'label',
              'iso',
              'not_visible',
              'is_default',
              'status',
          ];
          
          public function scopeWithoutCurrentLanguage($query)
          {
              $locale = app()->getLocale();
              return $query
                  ->where($this->getTable().'.iso','not like', $locale . '%');
          }
      }