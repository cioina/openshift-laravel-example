<?php namespace Acioina\UserManagement\Models;

      use Illuminate\Support\Str;
      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Article extends Model implements BaseModelContract {

          use BaseModel;

          protected $fillable = [
              'label',
              'cache_key',
              'facebook_image_id',
              'content',
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

          public function post_topics()
          {
              return $this->hasMany('Distilleries\Expendable\Models\PostTopic');
          }

          public function post_parts()
          {
              return $this->hasMany('Distilleries\Expendable\Models\PostPart');
          }
          
          public function post_images()
          {
              return $this->hasMany('Distilleries\Expendable\Models\PostImage');
          }

          public function post_videos()
          {
              return $this->hasMany('Distilleries\Expendable\Models\PostVideo');
          }

          public function post_settings()
          {
              return $this->hasMany('Distilleries\Expendable\Models\PostSetting');
          }

          public function getRouteKeyName()
          {
              return 'cache_key';
          }

      }