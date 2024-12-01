<?php namespace Distilleries\Expendable\Models;

      use Distilleries\Expendable\Scopes\Translatable;
      use Illuminate\Support\Str;
      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class WebPage extends Model implements BaseModelContract 
      {

          use BaseModel, Translatable;

          protected $fillable = [
              'label',
              'slug',
              'content',
              'is_public',
              'has_form',
              'is_raw',
              'status'
          ];
          
          protected static function boot()
          {
              parent::boot();

              static::creating(function ($post) {
                  $post->cache_key = (string) Str::uuid();
              });
          }

          public function web_page_images()
          {
              return $this->hasMany('Distilleries\Expendable\Models\WebPageImage');
          }

          public function web_page_videos()
          {
              return $this->hasMany('Distilleries\Expendable\Models\WebPageVideo');
          }

          public function web_page_settings()
          {
              return $this->hasMany('Distilleries\Expendable\Models\WebPageSetting');
          }
      }