<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class PostPart extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'post_id',
              'code_block_id'
          ];

          public function post()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Post');
          }

          public function code_block()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\CodeBlock');
          }

          public function getArea($id='')
          {
              $codeBlocks = CodeBlock::orderBy('updated_at', 'desc')
                  ->take(config('expendable.get_area_top_records'))->get();
              $codeBlocks = $codeBlocks->toArray();

              $groupedCodeBlocks = [];
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

              foreach ($post->post_parts as $postPart)
              {
                  $selected[$post->id][] = $postPart->code_block_id;
                  $groupedCodeBlocks[$postPart->code_block_id][] = [
                        'id'    => $postPart->code_block_id,
                        'label' => $postPart->code_block_id
                    ];
              }
              
              foreach ($codeBlocks as $codeBlock)
              {
                  if(!isset($groupedCodeBlocks[$codeBlock['id']]))
                  {
                      $groupedCodeBlocks[$codeBlock['id']][] = [
                          'id'    => $codeBlock['id'],
                          'label' => $codeBlock['label']
                      ];
                  }
              }
              
              $result[] = [
                  'label'   => $post->label,
                  'id'      => $post->id,
                  'choices' => $groupedCodeBlocks
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }