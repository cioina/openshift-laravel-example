<?php namespace Distilleries\Expendable\States;

      use Distilleries\Expendable\Helpers\TranslationUtils;
      use Distilleries\Expendable\Helpers\FormUtils;

      use Illuminate\Http\Request;
      use \FormBuilder;
      
      trait FormStateTrait 
      {
          /**
           * @var \Kris\LaravelFormBuilder\Form $form
           * Injected by the constructor
           */
          protected $form;

          public function getView($id)
          {
              $model = (!empty($id)) ? $this->findAutoDetectTranslation($id) : $this->model;
              $form  = FormBuilder::create(get_class($this->form), [
                  'model' => $model
              ]);

              $form_content = view('expendable::admin.form.components.formgenerator.info', [
                  'form'  => $form,
                  'id'    => $id,
                  'route' => $this->getControllerNameForAction() . '@',
              ]);

              $this->layoutManager->add([
                  'content' => view('expendable::admin.form.state.form', [
                      'form' => $form_content
                  ])
              ]);

              return $this->layoutManager->render();
          }

          public function getEdit($id = '')
          {
              $model = (!empty($id)) ? $this->findAutoDetectTranslation($id) : $this->model;
              $form  = FormBuilder::create(get_class($this->form), [
                  'model' => $model,
                  'url' => FormUtils::tryHttps(action($this->getControllerNameForAction() . '@postEdit', $id)),
              ]);

              if ($this->isTranslatableModel()) 
              {
                  $local_overide = $this->model->getIso($model->getTable(), $model->id);
                  if (!empty($local_overide)) 
                  {
                      $form->add(TranslationUtils::KEY_OVERRIDE_LOCAL, 'hidden', ['default_value' => $local_overide]);
                  }
              }

              $form_content = view('expendable::admin.form.components.formgenerator.full', [
                  'form' => $form
              ]);

              $this->layoutManager->add([
                  'content' => view('expendable::admin.form.state.form', [
                      'form' => $form_content
                  ])
              ]);

              return $this->layoutManager->render();
          }
          
          public function postEdit(Request $request)
          {

              $form = FormBuilder::create(get_class($this->form), [
                  'model' => $this->model
              ]);


              if ($form->hasError()) {
                  return $form->validateAndRedirectBack();
              }

              $result = $this->beforeSave();

              if ($result != null) {
                  return $result;
              }

              $result = $this->save($this->dataToSave($request), $request);


              if ($this->isTranslatableModel() && !$this->model->hasTranslation($this->model->getTable(), $this->model->getKey())) 
              {
                  $this->saveTranslation(app()->getLocale());
              }

              if ($result != null) {
                  return $result;
              }

              return redirect()->to(action($this->getControllerNameForAction() . '@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));

          }

          public function getTranslation($iso, $id)
          {
              $id_element = $this->model->hasBeenTranslated($this->model->getTable(), $id, $iso);
              if (! empty($id_element)) 
              {
                  return redirect()->to(action($this->getControllerNameForAction() . '@getEdit', $id_element), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
              }

              $model = (! empty($id)) ? $this->model->withoutTranslation()->findOrFail($id) : $this->model;
              $form  = FormBuilder::create(get_class($this->form), [
                  'model' => $model,
                  'url' => FormUtils::tryHttps(action($this->getControllerNameForAction() . '@postEdit', $id_element)),
              ])
                  ->remove('id')
                  ->add('translation_iso', 'hidden', ['default_value' => $iso])
                  ->add('translation_id_source', 'hidden', ['default_value' => $id])
                  ->add(TranslationUtils::KEY_OVERRIDE_LOCAL, 'hidden', ['default_value' => $iso]);

              $form_content = view('expendable::admin.form.components.formgenerator.full', [
                  'form' => $form
              ]);

              $this->layoutManager->add([
                  'content' => view('expendable::admin.form.state.form', [
                      'form' => $form_content
                  ])
              ]);

              return $this->layoutManager->render();
          }

          public function postTranslation(Request $request)
          {
              $form = FormBuilder::create(get_class($this->form), [
                  'model' => $this->model
              ]);

              if ($form->hasError()) {
                  return $form->validateAndRedirectBack();
              }

              $result = $this->beforeSave();

              if ($result != null) {
                  return $result;
              }

              $result = $this->save($this->dataToSave($request), $request);

              if ($this->isTranslatableModel()) {
                  $this->saveTranslation($request->get('translation_iso'), $request->get('translation_id_source'));
              }

              if ($result != null) {
                  return $result;
              }

              return redirect()->to(action($this->getControllerNameForAction() . '@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          protected function isTranslatableModel()
          {
              return method_exists($this->model, 'withoutTranslation');
          }

          protected function findAutoDetectTranslation($id, $orfail = true)
          {
              if ($orfail) 
              {
                  if ($this->isTranslatableModel()) 
                  {
                      return $this->model->withoutTranslation()->findOrFail($id);
                  } else {
                      return $this->model->findOrFail($id);
                  }
              } else {
                  if ($this->isTranslatableModel()) 
                  {
                      return $this->model->withoutTranslation()->find($id);
                  } else {
                      return $this->model->find($id);
                  }
              }

              return null;
          }
          
          protected function dataToSave(Request $request)
          {
              return $request->only($this->model->getFillable());
          }

          protected function beforeSave()
          {
              return null;
          }

          protected function afterSave()
          {
              return null;
          }

          protected function save($data, Request $request)
          {
              $primary = $request->get($this->model->getKeyName());
              if (empty($primary)) {
                  $this->model = $this->model->create($data);
              } else {
                  $this->model = $this->findAutoDetectTranslation($primary, false);
                  $this->model->update($data);
              }

              return $this->afterSave();
          }

          protected function saveTranslation($translation_iso, $translation_id_source = 0)
          {
              $this->model->setTranslation($this->model->getKey(), $this->model->getTable(), $translation_id_source, $translation_iso);
          }

          protected function getControllerNameForAction()
          {
              $action = explode('@', \Route::currentRouteAction());

              return '\\' . $action[0];
          }
      }