<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class VideoType extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'video_type_id',
              'video_type_name',
          ];
      }