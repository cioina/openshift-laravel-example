<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class YoutubeVideo extends Model implements BaseModelContract
      {
          use BaseModel;

          protected $fillable = [
              'label',
              'content',
              'status',
              'small_image_url',
              'original_image_url',
              'video_id',
              'start',
              'end',
              'small_image_quality',
              'original_image_quality',
              'domain',
              'video_type',
          ];
          
      }