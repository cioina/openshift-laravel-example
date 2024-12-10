<?php namespace Distilleries\Expendable\States;

      use Distilleries\Expendable\Formatter\Message; 
      use FormBuilder;
      use Illuminate\Http\Request;

      trait ExportStateTrait {

          protected $export_form = 'Distilleries\Expendable\Forms\Export\ExportForm';

          public function getExport()
          {
              $form = FormBuilder::create($this->export_form, [
                  'model' => $this->model
              ]);

              $form_content = view('expendable::admin.form.components.formgenerator.export', [
                  'form' => $form
              ]);
              $content      = view('expendable::admin.form.state.form', [

              ]);

              $this->layoutManager->add([
                  'form'=>$form_content,
                  'content'=>$content,
              ]);

              return $this->layoutManager->render();
          }

          public function postExport(Request $request)
          {
              $form = FormBuilder::create($this->export_form, [
                  'model' => $this->model
              ]);

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $data = $request->all();

              foreach ($data['range'] as $key => $date)
              {
                  $data['range'][$key] = date('Y-m-d', strtotime($date));
              }

              $result = $this->model->betweenCreate($data['range']['start'], $data['range']['end'])->get();

              if (! $result->isEmpty())
              {
                  $exporter = app($data['type']);

                  //if (version_compare(PHP_VERSION, '5.5.0', 'lt')) 
                  //{
                  //    return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.export_not_supported')]);
                  //}

                  $exporter->export($result->toArray(), $this->model->getTable() . '_' .$data['range']['start'] . '_' . $data['range']['end']);
              }else{
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.export_data_empty')]); 
              }

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.exported')]);
          }
      } 