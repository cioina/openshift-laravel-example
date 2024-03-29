<?php namespace Distilleries\Expendable\Http\Controllers\Admin\Base;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Illuminate\Database\Eloquent\Model;
      use Illuminate\Http\Request;

      class ModelBaseController extends BaseController 
      {
          protected $model;

          public function __construct(Model $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);
              $this->model = $model;
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
              $data->delete();

              return redirect()->to(action('\\' . get_class($this) . '@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          public function postSearch(Request $request, $query = null)
          {
              $ids = $request->get('ids');

              if (empty($query)) 
              {
                  $query = $this->model;
              }

              if (!empty($ids)) 
              {
                  return response()->json($this->generateResultSearchByIds($request, $query, $ids));
              }

              return response()->json($this->generateResultSearch($request, $query));
          }

          protected function generateResultSearchByIds(Request $request, $query, $ids)
          {
              $no_edit = $request->get('no_edit');

              if (!empty($no_edit) && method_exists($this->model, 'withoutTranslation')) {
                  $query = $query->withoutTranslation();
              }

              $data = $query->whereIn($this->model->getKeyName(), $ids)->get();

              return $data;
          }

          protected function generateResultSearch(Request $request, $query)
          {
              $term  = $request->get('term');
              $page  = $this->getParams($request, 'page', 1);
              $paged = $this->getParams($request, 'page_limit', 10);

              if (empty($term)) {
                  $elements = array();
                  $total    = 0;
              } else {
                  $elements = $query->search($term)->take($paged)->skip(($page - 1) * $paged)->get();
                  $total    = $query->search($term)->count();
              }

              return [
                  'total'    => $total,
                  'elements' => $elements
              ];
          }

          protected function getParams(Request $request, $key, $default_value)
          {
              $element = $request->get($key);

              if (empty($element)) {
                  $element = $default_value;
              }

              return $element;
          }
      }