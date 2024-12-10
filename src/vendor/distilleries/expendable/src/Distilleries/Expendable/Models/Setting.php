<?php namespace Distilleries\Expendable\Models;

      use Distilleries\Expendable\Scopes\Translatable;
      use Illuminate\Support\Str;
      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Setting extends Model implements BaseModelContract 
      {
          use BaseModel, Translatable;

          protected $fillable = [
              'label',
              'code_type',
              'code_block',
              'content',
          ];

          protected static function boot()
          {
              parent::boot();

              static::creating(function ($post) {
                  $post->cache_key = (string) Str::uuid();
              });
          }

      }