<?php namespace Distilleries\Expendable\Forms\WebPageVideo;

      use Distilleries\FormBuilder\FormValidator;

      class WebPageVideoForm extends FormValidator 
      {
          public function buildForm()
          {
              $areas = $this->model->getArea($this->model->web_page_id);
              
              $this 
              ->add($this->model->getKeyName(), 'hidden',
              [
                  'validation' => 'required',
                  'default_value'=> $this->model->web_page_id
              ])
              ->add('web_page_videos', 'choice_area', 
              [
                  'choices'  => $areas['choices'],
                  'selected' => $areas['selected'],
              ]);
              
              $this->addDefaultActions();
          }
      }