<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class WebPageVideo extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'web_page_id',
              'video_id'
          ];

          public function web_page()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\WebPage');
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
                  $webPages = WebPage::where('id','=', $id)->get();
                  foreach ($webPages as $tmp)
                  {
                      $webPage = $tmp;
                  }
              }else{
                  $webPage = WebPage::orderBy('updated_at', 'desc')->first();
              }
              
              if (empty($selected[$webPage->id]))
              {
                  $selected[$webPage->id] = [];
              }

              foreach ($webPage->web_page_videos as $youtubeVideo)
              {
                  $selected[$webPage->id][] = $youtubeVideo->video_id;
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
                  'label'   => $webPage->label,
                  'id'      => $webPage->id,
                  'choices' => $groupedVideos
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }