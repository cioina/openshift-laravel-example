<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class FacebookImage extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'label',
              'content',
              'status',
              'is_expired',
              //'album_id', 
              //'photo_id',
              //'small_image_url',
              //'original_image_url',
          ];
          
          /**
           * @codeCoverageIgnore
           */
          public function getByAlbumAndPhoto( $album_id, $photo_id ) {
              return $this->where('album_id', '=', $album_id)
                          ->where('photo_id', '=', $photo_id)
                          ->get();
          }
      }