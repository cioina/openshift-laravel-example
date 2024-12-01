<?php namespace Acioina\UserManagement\Http\Datatables\Post;

      use Acioina\UserManagement\Http\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Models\FacebookImage;
      use Illuminate\Support\Facades\Request;

      class PostDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[1, 'desc']];
              $this->add('label', function($model)
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
                         return $model->label . FormUtils::getImage( $image->small_image_url, 
                           $image->id,
                           $image->label,
                           'img_group1',
                           $image->original_image_url);
                       }else{
                        return $model->label; 
                      }
                    }else{
                      return $model->label;  
                  }
               },trans('user-management::datatable.title'));

              $this->add('created_at', function($model)
              {
                  return FormUtils::diffForHumans($model->created_at);
              },trans('user-management::datatable.created_at'));
              $this->addDefaultAction();
              $this->addTranslationAction();
          }
          
          public function applyFilters()
          {
              parent::applyFilters();
              
              $sSearch = Request::get('sSearch', null);
              if(!is_null($sSearch))
              {
                  Request::merge([
                      'sSearch_0' => $sSearch
                      ]);
              }
              
              $iSortCol = Request::get('iSortCol_0', null);
              if(!is_null($iSortCol) && is_numeric($iSortCol))
              {
                  if ( $iSortCol > 1 ){
                      Request::merge([
                          'iSortCol_0' => 1, 
                          'sSortDir_0' => 'desc'
                          ]);
                  }
              }

              $this->model = $this->model->where('status', '=', StaticLabel::STATUS_ONLINE);
          }
      }