<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\PostPart\PostPartForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\PostPart;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class PostPartController extends ModelBaseController  implements FormStateContract 
      {

          use FormStateTrait;

          public function __construct(PostPartForm $form, PostPart $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);
              $this->form = $form;
          }

          public function getIndex()
          {
              return redirect()->to(action('\\' . get_class($this) . '@getEdit'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          public function postEdit(Request $request)
          {
              $form = FormBuilder::create(get_class($this->form), [
                  'model' => $this->model,
              ]);

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $blog = $request->get($this->model->getKeyName());
              if(isset($blog))
              {
                  PostPart::where('post_id', '=', $blog)->delete();
              }
              
              $blogParts = $request->get('post_parts');
              if(isset($blogParts))
              {
                  foreach ($blogParts as $post_id=>$codeBlocks) 
                  {
                      foreach ($codeBlocks as $code_block_id) 
                      {
                          $partModel = new $this->model;
                          $partModel->post_id = $post_id;
                          $partModel->code_block_id = $code_block_id;
                          $partModel->save();
                      }
                  }
              }
              
              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }