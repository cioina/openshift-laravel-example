<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class PostSetting extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'post_id',
              'setting_id'
          ];

          public function post()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Post');
          }

          public function setting()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Setting');
          }

          public function getArea($id='')
          {
              $takeSome = config('expendable.get_area_top_records');
              $settings = Setting::orderBy('updated_at', 'desc')
                  ->take($takeSome)->get();
              $settings = $settings->toArray();

              $groupedSettings = [];
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

              foreach ($post->post_settings as $setting)
              {
                  $selected[$post->id][] = $setting->setting_id;
                  $groupedSettings[$setting->setting_id][] = [
                      'id'    => $setting->setting_id,
                      'label' => $setting->setting_id
                  ];
              }
              
              foreach ($settings as $setting)
              {
                  if(!isset($groupedSettings[$setting['id']]))
                  {
                      $groupedSettings[$setting['id']][] = [
                          'id'    => $setting['id'],
                          'label' => $setting['label']
                      ];
                  }
              }
              
              $result[] = [
                  'label'   => $post->label,
                  'id'      => $post->id,
                  'choices' => $groupedSettings
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }