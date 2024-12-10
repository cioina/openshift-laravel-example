<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class WebPageSetting extends Model implements BaseModelContract 
      {
          use BaseModel;

          public $timestamps = false;
          
          protected $fillable = [
              'id',
              'web_page_id',
              'setting_id'
          ];

          public function web_page()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\WebPage');
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

              foreach ($webPage->web_page_settings as $setting)
              {
                  $selected[$webPage->id][] = $setting->setting_id;
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
                  'label'   => $webPage->label,
                  'id'      => $webPage->id,
                  'choices' => $groupedSettings
              ];
              
              return ['choices'  => $result,
                      'selected' => $selected];
          }
      }