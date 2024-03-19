<?php namespace Distilleries\Expendable\Forms\PostTopic;

      use Distilleries\FormBuilder\FormValidator;

      class PostTopicForm extends FormValidator 
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
                  ->add('post_topics', 'choice_area', 
                  [
                      'choices'  => $areas['choices'],
                      'selected' => $areas['selected'],
                  ]);
              
              $this->addDefaultActions();
          }
      }