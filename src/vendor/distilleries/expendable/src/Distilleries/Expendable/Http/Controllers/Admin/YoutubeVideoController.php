<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\YoutubeVideo\YoutubeVideoDatatable;
      use Distilleries\Expendable\Forms\YoutubeVideo\YoutubeVideoForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\YoutubeVideo;
      use Illuminate\Http\Request;

      class YoutubeVideoController extends BaseComponent 
      {
          public function __construct(YoutubeVideoDatatable $datatable, YoutubeVideoForm $form, YoutubeVideo $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }

          protected function save($data, Request $request)
          {
              $primary = $request->get($this->model->getKeyName());
 
              $data['small_image_url']    = 'https://' . $data['domain'] . '.ytimg.com/vi/' . $data['video_id'] . '/' . $data['small_image_quality'];
              $data['original_image_url'] = 'https://' . $data['domain'] . '.ytimg.com/vi/' . $data['video_id'] . '/' . $data['original_image_quality'];
              if (empty($data['start']))
              {
                 $data['start'] = null; 
              }
              if (empty($data['end']))
              {
                  $data['end'] = null; 
              }

              if (empty($primary)) 
              {
                  $this->model = $this->model->create($data);
              } else {
                  $this->model = $this->findAutoDetectTranslation($primary, false);
                  $this->model->update($data);
              }

              return $this->afterSave();
          }

      }