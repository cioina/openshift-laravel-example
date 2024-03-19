<?php namespace Distilleries\Expendable\Datatables\WebPage;

      use Distilleries\Expendable\Datatables\BaseDatatable;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Illuminate\Support\Facades\Request;

      use Distilleries\Expendable\Models\WebPage;
      use Distilleries\Expendable\Models\WebPageImage;
      use Distilleries\Expendable\Models\WebPageVideo;
      use Distilleries\Expendable\Models\WebPageSetting;

      class WebPageDatatable extends BaseDatatable {

          public function build()
          {
              $this->defaultOrder = [[2, 'desc']];
              $this->add('id', null, trans('expendable::datatable.id'));
              $this->add('label', null, trans('expendable::datatable.title'));
              $this->add('updated_at', null, trans('expendable::datatable.updated_at'));
              $this->addTranslationAction();

              $this->add('edit_parts', function($model)
              {
                 $image = WebPageImage::where('web_page_id','=', $model->id)->first();
                 $video = WebPageVideo::where('web_page_id','=', $model->id)->first();
                 $setting = WebPageSetting::where('web_page_id','=', $model->id)->first();

                 return view('expendable::admin.form.components.datatable.action',[
                      'image_data'  => isset($image) && !empty($image)?['id'=>$image->id]:'',
                      'image_route' => '\Distilleries\Expendable\Http\Controllers\Admin\WebPageImageController@',

                      'video_data'  => isset($video) && !empty($video)?['id'=>$video->id]:'',
                      'video_route' => '\Distilleries\Expendable\Http\Controllers\Admin\WebPageVideoController@',

                      'setting_data'  => isset($setting) && !empty($setting)?['id'=>$setting->id]:'',
                      'setting_route' => '\Distilleries\Expendable\Http\Controllers\Admin\WebPageSettingController@',
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
                  if ( $iSortCol > 2){
                      Request::merge([
                          'iSortCol_0' => 2, 
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
                  $rows = WebPage::distinct()->groupBy($searchQuery)->get();

                  foreach ($rows as $row)
                  {
                    $noExpiredImages = true;
                    $images = $row->web_page_images;
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