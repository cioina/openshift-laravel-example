<?php namespace Distilleries\Expendable\Forms\WebStatistics;

      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\FormBuilder\FormValidator;

      class WebStatisticsForm extends FormValidator {

          public static $rules = [];

          public function buildForm()
          {
              $this
                  ->add($this->model->getKeyName(), 'hidden')
                  ->add('browser_id', 'text', [
                      'label'      => trans('expendable::form.ip_status')
                  ])
                  ->addDefaultActions();
          }
      }