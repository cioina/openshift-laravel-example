<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class PostTopic extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'post_id',
              'topic_id'
          ];

          public function post()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Post');
          }

          public function topic()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Topic');
          }

          public function getArea($id = '')
          {
              $topics = Topic::orderBy('updated_at', 'desc')
                  ->take(config('expendable.get_area_top_records'))->get();
              $topics = $topics->toArray();

              $groupedTopics = [];
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

              foreach ($post->post_topics as $postTopic)
              {
                  $selected[$post->id][] = $postTopic->topic_id;
                  $groupedTopics[$postTopic->topic_id][] = [
                        'id'    => $postTopic->topic_id,
                        'label' => $postTopic->topic_id
                    ];
              }
              
              foreach ($topics as $topic)
              {
                  if(!isset($groupedTopics[$topic['id']]))
                  {
                      $groupedTopics[$topic['id']][] = [
                          'id'    => $topic['id'],
                          'label' => $topic['label']
                      ];
                  }
              }
              
              $result[] = [
                  'label'   => $post->label,
                  'id'      => $post->id,
                  'choices' => $groupedTopics
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }