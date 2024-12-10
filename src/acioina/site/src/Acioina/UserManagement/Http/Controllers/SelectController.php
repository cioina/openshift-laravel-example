<?php namespace Acioina\UserManagement\Http\Controllers;

      use Acioina\UserManagement\Http\Controllers\Base\ModelBaseJsonController;

      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use Distilleries\Expendable\Models\UsState;
      use Distilleries\Expendable\Models\Country;
      use Illuminate\Http\Request;

      class SelectController extends ModelBaseJsonController implements FormStateContract
      {
          public function __construct()
          {
          }

          public function getEdit($id = 0)
          {
              if (! $this->createJsonFromInput($id))
              {
                  return;
              }

              if( $id == 0 )
              {
                  $rows = UsState::orderBy('state_name', 'asc')->get();

                  foreach ($rows as $row)
                  {
                      $rec[$row->state_name] = $row->state_code;
                  }

              }elseif( $id == 1 )
              {
                  $rows = Country::orderBy('country_name', 'asc')->get();

                  foreach ($rows as $row)
                  {
                      $rec[$row->country_name] = $row->country_code;
                  }
              }else{
                  $this->sendJsonMessage(trans('user-management::alpaca.wrong_id_parameter'));
              }

              $this->sendJson($rec, 'records');
          }

          public function postEdit(Request $request)
          {
          }
      }