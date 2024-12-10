<?php namespace Distilleries\Expendable\Forms\Login;

      use Distilleries\FormBuilder\FormValidator;
      use Illuminate\Contracts\Console\Kernel;
      use \CIOINA_Util;

      class SignIn extends FormValidator
      {
          public static $rules = [
              'email'    => 'required|email',
              'password' => 'required',
          ];

         protected $artisan;

          public function __construct(Kernel $artisan)
          {
              $this->artisan   = $artisan;
          }

          public function buildForm()
          {
              $query = 'SELECT COUNT('. CIOINA_Util::backquote('id'). ') FROM '
                 . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase'))
                 . '.' . CIOINA_Util::backquote('sent_emails');
              $ids = $GLOBALS['CIOINA_dbi']->fetchValue($query);

              if ($ids == 0)
              {
                  $this->artisan->call('user-management:importAllExcelFiles');
              }

              $this->add('email', 'email',
                  [
                      'label'      => trans('expendable::form.email'),
                      'validation' => 'required,custom[email]',
                      'attr'       => [
                          'class' => 'placeholder-no-fix',
                      ],

                  ])
                  ->add('password', 'password',
                      [
                          'label'      => trans('expendable::form.password'),
                          'validation' => 'required',
                          'attr'       => [
                              'class' => 'placeholder-no-fix'
                          ],
                      ])
                  ->add('login', 'submit',
                      [
                          'label' => trans('expendable::form.login'),
                          'attr'  => [
                              'class' => 'btn green-haze pull-right'
                          ],
                      ]);
          }
      }