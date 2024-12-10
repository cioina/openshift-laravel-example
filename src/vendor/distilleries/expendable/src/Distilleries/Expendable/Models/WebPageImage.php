<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class WebPageImage extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'web_page_id',
              'image_id'
          ];

          public function web_page()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\WebPage');
          }

          public function image()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\FacebookImage');
          }

          public function getArea($id='')
          {
              $takeSome = config('expendable.get_area_top_records');
              $facebookImages = FacebookImage::orderBy('updated_at', 'desc')
                  ->take($takeSome)->get();
              $facebookImages = $facebookImages->toArray();

              $groupedImages = [];
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

              foreach ($webPage->web_page_images as $faceBookImage)
              {
                  $selected[$webPage->id][] = $faceBookImage->image_id;
                  $groupedImages[$faceBookImage->image_id][] = [
                      'id'    => $faceBookImage->image_id,
                      'label' => $faceBookImage->image_id
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
                  'label'   => $webPage->label,
                  'id'      => $webPage->id,
                  'choices' => $groupedImages
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }