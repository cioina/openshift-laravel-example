<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class PostImage extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'post_id',
              'image_id'
          ];

          public function post()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Post');
          }

          public function image()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\FacebookImage');
          }

          public function getArea($id='')
          {
              $facebookImages = FacebookImage::orderBy('updated_at', 'desc')
                  ->take(config('expendable.get_area_top_records'))->get();
              $facebookImages = $facebookImages->toArray();

              $groupedImages = [];
              $result = [];
              $selected= [];
              if(! empty($id))
              {
                  $postById = Post::where('id','=', $id)->get();
                  foreach ($postById as $tmp)
                  {
                      $post = $tmp;
                  }
              }else{
                  $post = Post::orderBy('updated_at', 'desc')->first();
              }
              
              if (empty($selected[$post->id]))
              {
                  $selected[$post->id] = [];
              }

              foreach ($post->post_images as $postImage)
              {
                  $selected[$post->id][] = $postImage->image_id;
                  $groupedImages[$postImage->image_id][] = [
                        'id'    => $postImage->image_id,
                        'label' => $postImage->image_id
                    ];

              }
              
              foreach ($facebookImages as $facebookImage)
              {
                  if(!isset($groupedImages[$facebookImage['id']]))
                  {
                      $groupedImages[$facebookImage['id']][] = [
                          'id'    => $facebookImage['id'],
                          'label' => $facebookImage['label']
                      ];
                  }
              }
              
              $result[] = [
                  'label'   => $post->label,
                  'id'      => $post->id,
                  'choices' => $groupedImages
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }