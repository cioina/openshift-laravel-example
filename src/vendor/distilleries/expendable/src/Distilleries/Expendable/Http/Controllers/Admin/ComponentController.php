<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Formatter\Message;
      use Distilleries\Expendable\Forms\Component\ComponentForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseController;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\FormBuilder\Contracts\FormStateContract;

      use Illuminate\Contracts\Console\Kernel;
      use Illuminate\Http\Request;
      use \FormBuilder;

      class ComponentController extends BaseController implements FormStateContract 
      {
          protected $artisan;

          public function __construct(Kernel $artisan, ComponentForm $form, LayoutManagerContract $layoutManager)
          {
              parent::__construct($layoutManager);
              $this->form    = $form;
              $this->artisan = $artisan;
          }

          public function getIndex()
          {
              return redirect()->to(action('\\' . get_class($this) . '@getEdit'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          public function getEdit($id = '')
          {
              $form = FormBuilder::create(get_class($this->form),[
                  'url' => FormUtils::tryHttps(action('\\' . get_class($this) . '@postEdit')),
              ]);

              $form_content = view('expendable::admin.form.components.formgenerator.full', [
                  'form' => $form
              ]);
              $content      = view('expendable::admin.form.state.form', [

              ]);

              $this->layoutManager->add([
                  'form'    => $form_content,
                  'content' => $content,
              ]);

              return $this->layoutManager->render();
          }

          public function postEdit(Request $request)
          {
              $form = FormBuilder::create(get_class($this->form));

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $label            = $request->get('label');
              $label_form       = $label . 'Form';
              $label_datatable  = $label . 'Datatable';
              $label_controller = $label . 'Controller';
              $model            = $request->get('models');
              $states           = $request->get('state');

              foreach ($states as $state)
              {
                  if (strpos($state, 'DatatableStateContract') !== false)
                  {
                      $this->artisan->call('datatable:make', [
                          '--fields' => $request->get('colon_datatable'),
                          'name'     => 'Datatables/' . $label_datatable
                      ]);
                  } else if (strpos($state, 'FormStateContract') !== false)
                  {

                      $this->artisan->call('form:make', [
                          '--fields' => $request->get('fields_form'),
                          'name'     => 'Forms/' . $label_form
                      ]);
                  }
              }

              $this->artisan->call('expendable:component.make', [
                  '--states'    => join(',', $states),
                  '--model'     => $model,
                  '--datatable' => $label_datatable,
                  '--form'      => $label_form,
                  'name'        => 'Http/Controllers/Admin/' . $label_controller
              ]);

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.generated')]);
          }
      }