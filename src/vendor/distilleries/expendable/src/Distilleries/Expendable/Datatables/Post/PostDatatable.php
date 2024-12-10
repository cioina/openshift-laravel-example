<?php namespace Distilleries\Expendable\Datatables\Post;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;

      use Distilleries\Expendable\Models\Post;
      use Distilleries\Expendable\Models\FacebookImage;
      use Distilleries\Expendable\Models\PostPart;
      use Distilleries\Expendable\Models\PostImage;
      use Distilleries\Expendable\Models\PostTopic;
      use Distilleries\Expendable\Models\PostVideo;
      use Distilleries\Expendable\Models\PostSetting;

      use Illuminate\Support\Facades\Request;

      class PostDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[3, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('facebook_image_id', function($model)
              {
                   if(isset($model->facebook_image_id) && $model->facebook_image_id > 0)
                   {
                      $post_parts = $model->post_images;
                      if(isset($post_parts) && !empty($post_parts))
                      {
                          foreach ($post_parts as $value)
                          {
                              if($value->image->id === $model->facebook_image_id) 
                              {
                                 $image = $value->image;
                                 break;
                              }
                          }
                       }
                       if(! isset($image))
                       {
                            $image = FacebookImage::where('id','=', $model->facebook_image_id)->first();
                       }
                      if(isset($image) && !empty($image))
                      {
                        return FormUtils::getImage( $image->small_image_url, 
                          $image->id,
                          $image->label,
                          'img_group1',
                          $image->original_image_url);
                      }else{
                       return trans('expendable::datatable.no_image'); 
                     }
                   }else{
                     return trans('expendable::datatable.no_image');    
                 }
              },
              trans('expendable::datatable.image'));
              
              $this->add('label', null, trans('expendable::datatable.title'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));
              $this->addTranslationAction();
              
              $this->add('edit_parts', function($model)
              {
                 $part  = PostPart::where('post_id','=', $model->id)->first();
                 $image = PostImage::where('post_id','=', $model->id)->first();
                 $topic = PostTopic::where('post_id','=', $model->id)->first();
                 $video = PostVideo::where('post_id','=', $model->id)->first();
                 $setting = PostSetting::where('post_id','=', $model->id)->first();
                
                  return view('expendable::admin.form.components.datatable.action',
                      [
                      //'code_block_data'  => isset($part) && !empty($part)?$part->toArray():'',

                      'code_block_data'  => isset($part) && !empty($part)?['id'=>$part->id]:'',
                      'code_block_route' => '\Distilleries\Expendable\Http\Controllers\Admin\PostPartController@',

                      'image_data'  => isset($image) && !empty($image)?['id'=>$image->id]:'',
                      'image_route' => '\Distilleries\Expendable\Http\Controllers\Admin\PostImageController@',

                      'topic_data'  => isset($topic) && !empty($topic)?['id'=>$topic->id]:'',
                      'topic_route' => '\Distilleries\Expendable\Http\Controllers\Admin\PostTopicController@',

                      'video_data'  => isset($video) && !empty($video)?['id'=>$video->id]:'',
                      'video_route' => '\Distilleries\Expendable\Http\Controllers\Admin\PostVideoController@',

                      'setting_data'  => isset($setting) && !empty($setting)?['id'=>$setting->id]:'',
                      'setting_route' => '\Distilleries\Expendable\Http\Controllers\Admin\PostSettingController@',
                      ])->render();
              },
              trans('expendable::datatable.edit_parts'));

              $this->addDefaultAction();
          }
          
          public function applyFilters()
          {
              parent::applyFilters();

              $status = Request::get('status', null);
              if (isset($status) && $status === '0')
              {
                  $this->model = $this->model->where('status', '=', 0);
              }
              
              $status = Request::get('expired', null);
              if (! empty($status) && $status === '1')
              {
                  $this->model = $this->expiredImages($this->model,'id');
              }

              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 3 || $iSortCol == 1){
                      Request::merge([
                          'iSortCol_0' => 3, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }
          }

          public function filters()
          {
              $this->form->add('status', 'choice', [
                  'choices'     => StaticLabel::status(),
                  'empty_value' => '-',
                  'validation'  => 'required',
                  'label'       => trans('expendable::datatable.status')
              ]);

              $this->form->add('expired', 'choice', [
                'choices'     => StaticLabel::yesNo(),
                'empty_value' => '-',
                'validation'  => 'required',
                'label'       => trans('expendable::datatable.is_expired')
              ]);
          }

          private function expiredImages($query, $searchQuery)
          {
              return $query->where(function($query) use ($searchQuery)
              {
                  $rows = Post::distinct()->groupBy($searchQuery)->get();

                  foreach ($rows as $row)
                  {
                    $noExpiredImages = true;
                    $images = $row->post_images;
                    if(isset($images) && !empty($images))
                    {
                        foreach ($images as $image)
                        {
                            if($image->image->is_expired)
                            {
                               $noExpiredImages = false;
                               break;
                            }
                        }
                    }

                    if($noExpiredImages)
                    {
                        $query->where($searchQuery, '!=', $row->{$searchQuery});
                    }
                  }
              });
          }
      }