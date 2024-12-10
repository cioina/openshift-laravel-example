<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\Client\ClientDatatable;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\FormLessComponent;
      use Distilleries\Expendable\Models\Client;
      use Illuminate\Http\Request;
      use \CIOINA_Util;

      class ClientController extends FormLessComponent 
      {

          public function __construct(ClientDatatable $datatable, Client $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
          }

          public function putDestroy(Request $request)
          {
              $validation = \Validator::make($request->all(), [
                  'id' => 'required'
              ]);
              if ($validation->fails()) {
                  return redirect()->back()->withErrors($validation)->withInput($request->all());
              }

              $data = $this->model->where($this->model->getKeyName(), $request->get('id'))->get()->last();

              $query = 'DELETE FROM ' 
                . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                . '.' . CIOINA_Util::backquote('online_clients')
                . ' WHERE ' . CIOINA_Util::backquote('client_id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes($data->id) . '\'';
              $GLOBALS['CIOINA_dbi']->tryQuery($query);

              $query = 'DELETE FROM ' 
                . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                . '.' . CIOINA_Util::backquote('surveys')
                . ' WHERE ' . CIOINA_Util::backquote('client_id') . '='
                . '\'' . CIOINA_Util::sqlAddSlashes($data->id) . '\'';
              $GLOBALS['CIOINA_dbi']->tryQuery($query);

              $data->delete();

              return redirect()->to(action('\\' . get_class($this) . '@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

      }