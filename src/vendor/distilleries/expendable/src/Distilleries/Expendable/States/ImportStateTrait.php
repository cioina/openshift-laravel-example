<?php namespace Distilleries\Expendable\States;

      use Distilleries\Expendable\Formatter\Message;
      use Distilleries\Expendable\Models\Translation;

      use Illuminate\Http\Request;
      use Illuminate\Support\Str;
      use FormBuilder;

      trait ImportStateTrait 
      {
          protected $import_form = 'Distilleries\Expendable\Forms\Import\ImportForm';

          public function getImport()
          {
              $form = FormBuilder::create($this->import_form, [
                  'model' => $this->model
              ]);

              $form_content = view('expendable::admin.form.components.formgenerator.import', [
                  'form' => $form
              ]);
              $content = view('expendable::admin.form.state.form', [

              ]);

              $this->layoutManager->add([
              'form'=>$form_content,
              'content'=>$content,
              ]);

              return $this->layoutManager->render();
          }

          public function postImport(Request $request)
          {

              $form = FormBuilder::create($this->import_form, [
                  'model' => $this->model
              ]);

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $hasFileName = false;
              $dataDir = $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir');
              $managerRoot = config('expendable.manager_root_dir');

              $baseDir = basename($dataDir);
              if( ! empty($baseDir))
              {
                  $start = '/' . $baseDir . '/' . $managerRoot . '/';
                  $file = trim($request->get('file'));
                  if (false !== Str::startsWith($file, $start)) 
                  {
                      $file = substr($file, strlen($start));
                  }

                  if( strlen($file) > 0 )
                  {
                      $file = "$dataDir/$managerRoot/$file";
                      $hasFileName = true;
                  }
              }

              if (! $hasFileName || ! app('files')->exists($file) )
              {
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.file_not_found')]);
              }

              $contract = ucfirst(app('files')->extension($file)) . 'ImporterContract';
              $exporter = app($contract);

              //if (version_compare(PHP_VERSION, '5.5.0', 'lt')) 
              //{
              //    return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.export_not_supported')]);
              //}

              $data = $exporter->getArrayDataFromFile($file);
              if (empty($data) || count($data) == 0 )
              {
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.export_data_empty')]); 
              }

              foreach ($data as $item)
              {
                  $primary = isset($item[$this->model->getKeyName()]) ? $item[$this->model->getKeyName()] : '';
                  if (empty($primary))
                  {
                      $this->model = new $this->model;
                      $this->model = $this->model->create($item);
                      if (method_exists($this->model, 'withoutTranslation'))
                      {
                           Translation::create([
                              'id_element' => $this->model->id,
                              'model'      => $this->model->getTable(),
                              'id_source'  => 0,
                              'iso'        => app()->getLocale(),
                          ]);
                      }
                  } else
                  {
                      $this->model = $this->model->find($primary);
                      $this->model->update($item);
                  }
              }

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.imported')]);
          }
      } 