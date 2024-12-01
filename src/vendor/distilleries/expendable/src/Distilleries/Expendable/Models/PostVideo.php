<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class PostVideo extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'post_id',
              'video_id'
          ];

          public function post()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Post');
          }

          public function video()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\YoutubeVideo');
          }

          public function getArea($id='')
          {
              $takeSome = config('expendable.get_area_top_records');
              $youtubeVideos = YoutubeVideo::orderBy('updated_at', 'desc')
                  ->take($takeSome)->get();
              $youtubeVideos = $youtubeVideos->toArray();

              $groupedVideos = [];
              $result = [];
              $selected= [];
              if(! empty($id))
              {
                  $posts = Post::where('id','=', $id)->get();
                  foreach ($posts as $tmp)
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

              foreach ($post->post_videos as $youtubeVideo)
              {
                  $selected[$post->id][] = $youtubeVideo->video_id;
                  $groupedVideos[$youtubeVideo->video_id][] = [
                      'id'    => $youtubeVideo->video_id,
                      'label' => $youtubeVideo->video_id
                  ];
              }
              
              foreach ($youtubeVideos as $youtubeVideo)
              {
                  if(!isset($groupedVideos[$youtubeVideo['id']]))
                  {
                      $groupedVideos[$youtubeVideo['id']][] = [
                          'id'    => $youtubeVideo['id'],
                          'label' => $youtubeVideo['label']
                      ];
                  }
              }
              
              $result[] = [
                  'label'   => $post->label,
                  'id'      => $post->id,
                  'choices' => $groupedVideos
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }