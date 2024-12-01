<?php namespace Distilleries\Expendable\Forms\PostPart;

      use Distilleries\FormBuilder\FormValidator;

      class PostPartForm extends FormValidator 
      {
          public function buildForm()
          {
              $areas = $this->model->getArea($this->model->post_id);
              
              $this 
                  ->add($this->model->getKeyName(), 'hidden',
                  [
                      'validation'    => 'required',
                      'default_value' => $this->model->post_id
                  ])
                  ->add('post_parts', 'choice_area', 
                  [
                      'choices'  => $areas['choices'],
                      'selected' => $areas['selected'],
                  ]);
              
              $this->addDefaultActions();
          }
      }