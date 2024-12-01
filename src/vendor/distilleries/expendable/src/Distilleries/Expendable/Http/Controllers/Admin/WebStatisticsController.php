<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\WebStatistics\WebStatisticsDatatable;
      use Distilleries\Expendable\Forms\WebStatistics\WebStatisticsForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Models\WebStatistics;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Illuminate\Contracts\Console\Kernel;
      use Illuminate\Http\Request;
      use \CIOINA_Util;

      class WebStatisticsController extends BaseComponent
      {
          protected $artisan;

          public function __construct(Kernel $artisan, WebStatisticsDatatable $datatable, WebStatisticsForm $form, WebStatistics $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->artisan   = $artisan;
              $this->datatable = $datatable;
              $this->form      = $form;
          }

          public function putDestroy(Request $request)
          {
              $validation = \Validator::make($request->all(),
                  [
                  'id' => 'required'
                  ]);
              if ($validation->fails()) {
                  return redirect()->back()->withErrors($validation)->withInput($request->all());
              }

              $data = $this->model->where($this->model->getKeyName(), $request->get('id'))->get()->last();

              if ($data->browser_id > 1)
              {
                  if($data->browser_id == 2)
                  {
                      WebStatistics::truncate();
                      \DB::update('ALTER TABLE '.$this->model->getTable().' AUTO_INCREMENT = 0;');

                      $sessionTime = -1 * ($GLOBALS['CIOINA_Config']->get('SessionPeriodInMinutes') + 2);

                      $query = 'DELETE FROM '
                      . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                      . '.' . CIOINA_Util::backquote('online_clients')
                      . ' WHERE ' . CIOINA_Util::backquote('updated_at') . '< STR_TO_DATE('
                      . '\'' . CIOINA_Util::sqlAddSlashes(FormUtils::getDateIntervalTime(0, $sessionTime)->format('Y-m-d H:i:s')) . '\','
                      . '\'' . CIOINA_Util::sqlAddSlashes('%Y-%m-%d %H:%i:%s') .  '\')';
                      $GLOBALS['CIOINA_dbi']->tryQuery($query);

                      $this->artisan->call('user-management:clearLaravelSession');
                      $this->artisan->call('config:clear');
                      $this->artisan->call('route:clear');

                  }else{
                      //\DB::table($this->model->getTable())->where('request_ip_address', $data->request_ip_address)->delete();
                      $query = 'DELETE FROM '
                        . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                        . '.' . CIOINA_Util::backquote('web_statistics')
                        . ' WHERE (' . CIOINA_Util::backquote('request_ip_address') . '='
                        . '\'' . CIOINA_Util::sqlAddSlashes($data->request_ip_address) . '\''
                            . (empty($data->request_session) ? '':' OR ' . CIOINA_Util::backquote('request_session') . '='
                            . '\'' . CIOINA_Util::sqlAddSlashes($data->request_session) . '\'')
                        . ')';
                      $GLOBALS['CIOINA_dbi']->tryQuery($query);

                  }
              }else{
                  $this->artisan->call('config:cache');
                  $this->artisan->call('route:cache');
                  $data->delete();
              }

              return redirect()->to(action('\\' . get_class($this) . '@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }