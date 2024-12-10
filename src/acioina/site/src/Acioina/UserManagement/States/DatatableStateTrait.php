<?php namespace Acioina\UserManagement\States;

      trait DatatableStateTrait 
      {
          /**
           * @var \Distilleries\DatatableBuilder\EloquentDatatable $datatable
           * Injected by the constructor
           */
          protected $datatable;

          public function getIndexDatatable()
          {
              $this->datatable->build();
              $datatable = $this->datatable->generateHtmlRender('user-management::user.part.datatable');
              
              $this->layoutManager->add([
                  'content'=>view('user-management::user.form.state.datatable')->with(
                  [
                      'datatable' => $datatable
                  ])
              ]);

              return $this->layoutManager->render();
          }

          public function getDatatable()
          {
              $this->datatable->setModel($this->model);
              $this->datatable->build();
              
              return $this->datatable->generateColomns(false);
          }
      }