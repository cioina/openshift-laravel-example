<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\Service\ServiceDatatable;
      use Distilleries\Expendable\Forms\Service\ServiceForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;

      use Distilleries\Expendable\Models\UsState;
      use Distilleries\Expendable\Models\Country;
      use Distilleries\Expendable\Models\EmailType;
      use Distilleries\Expendable\Models\Email;
      use Distilleries\Expendable\Models\Language;
      use Distilleries\Expendable\Models\Role;
      use Distilleries\Expendable\Models\Service;
      use Distilleries\Expendable\Models\Topic;
      use Distilleries\Expendable\Models\WebPage;
      use Distilleries\Expendable\Models\CodeBlock;
      use Distilleries\Expendable\Models\Setting;
      use Distilleries\Expendable\Models\FacebookImage;
      use Distilleries\Expendable\Models\VideoType;
      use Distilleries\Expendable\Models\YoutubeVideo;
      use Distilleries\Expendable\Models\Post;

      use Distilleries\Expendable\Models\Client;
      use Distilleries\Expendable\Models\Survey;
      use Distilleries\Expendable\Models\OnlineClient;
      use Distilleries\Expendable\Models\GuestEmail;
      use Distilleries\Expendable\Models\SentEmail;
      use Distilleries\Expendable\Models\User;
      use Distilleries\Expendable\Models\Permission;

      use Distilleries\Expendable\Models\PostImage;
      use Distilleries\Expendable\Models\PostPart;
      use Distilleries\Expendable\Models\PostTopic;
      use Distilleries\Expendable\Models\PostVideo;
      use Distilleries\Expendable\Models\PostSetting;

      use Distilleries\Expendable\Models\WebPageImage;
      use Distilleries\Expendable\Models\WebPageVideo;
      use Distilleries\Expendable\Models\WebPageSetting;

      use Distilleries\Expendable\Models\Translation;

      use Distilleries\Expendable\Formatter\Message;

      use Illuminate\Contracts\Routing\Registrar;
      use Illuminate\Contracts\Console\Kernel;

      class ServiceController extends BaseComponent
      {
          protected $artisan;

          public function __construct(Kernel $artisan, ServiceDatatable $datatable, ServiceForm $form, Service $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->artisan   = $artisan;
              $this->datatable = $datatable;
              $this->form      = $form;
          }

          public function getExportAllData()
          {
              @set_time_limit($GLOBALS['CIOINA_Config']->get('PhpExecutionInMinutes') * 60);

              if(! defined('TESTSUITE'))
              {
                  $this->artisan->call('user-management:clearExcelFiles');
              }

              UsState::exportAll();
              Country::exportAll();
              EmailType::exportAll();
              Email::exportAll();
              Language::exportAll();
              Role::exportAll();
              Service::exportAll();
              Topic::exportAll();
              WebPage::exportAll();
              CodeBlock::exportAll();
              Setting::exportAll();
              FacebookImage::exportAll();
              VideoType::exportAll();
              YoutubeVideo::exportAll();
              Post::exportAll();

              Client::exportAll();
              Survey::exportAll();
              OnlineClient::exportAll();
              GuestEmail::exportAll();
              SentEmail::exportAll();
              User::exportAll();
              Permission::exportAll();

              PostImage::exportAll();
              PostPart::exportAll();
              PostTopic::exportAll();
              PostVideo::exportAll();
              PostSetting::exportAll();

              WebPageImage::exportAll();
              WebPageVideo::exportAll();
              WebPageSetting::exportAll();

              //Important: This model has data from all models which have Translatable: Email, Post, Setting, Toping, WebPage
              Translation::exportAll();;

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.exported')]);
          }

          public function getImportAllData()
          {
              $canImport = true &&
              Language::excelFileExists() &&
              EmailType::excelFileExists() &&
              Role::excelFileExists() &&
              Service::excelFileExists() &&
              User::excelFileExists() &&
              Permission::excelFileExists();

              if (! $canImport)
              {
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.import_excel_files',['files' =>'Language, EmailType, Role, Service, User, Permission'])]);
              }

              if(config('database.default') === 'testbench')
              {
                  \DB::update("PRAGMA foreign_keys = OFF;");
              }else{
                  \DB::update("SET FOREIGN_KEY_CHECKS = 0;");
              }

              UsState::truncate();
              Country::truncate();
              EmailType::truncate();
              Email::truncate();
              Language::truncate();
              Role::truncate();
              Service::truncate();
              Topic::truncate();
              WebPage::truncate();
              CodeBlock::truncate();
              Setting::truncate();
              FacebookImage::truncate();
              VideoType::truncate();
              YoutubeVideo::truncate();
              Post::truncate();

              Client::truncate();
              Survey::truncate();
              OnlineClient::truncate();
              GuestEmail::truncate();
              SentEmail::truncate();
              User::truncate();
              Permission::truncate();

              PostImage::truncate();
              PostPart::truncate();
              PostTopic::truncate();
              PostVideo::truncate();
              PostSetting::truncate();

              WebPageImage::truncate();
              WebPageVideo::truncate();
              WebPageSetting::truncate();

              Translation::truncate();


              \DB::table('password_resets')->truncate();
              \DB::table('migrations')->truncate();

              if(config('database.default') === 'testbench')
              {
                  \DB::update("PRAGMA foreign_keys = ON;");
              }else{
                  \DB::update("SET FOREIGN_KEY_CHECKS = 1;");
              }


              @set_time_limit($GLOBALS['CIOINA_Config']->get('PhpExecutionInMinutes') * 60);

              //https://stackoverflow.com/questions/21184962/use-of-undefined-constant-stdin-assumed-stdin-in-c-wamp-www-study-sayhello
              //define('STDIN',fopen("php://stdin","r"));
              //$this->artisan->call('migrate:fresh');
              $this->artisan->call('migrate:fresh',['--force' => true,]);

              UsState::importAll();
              Country::importAll();
              EmailType::importAll();
              Email::importAll();
              Language::importAll();
              Role::importAll();
              Service::importAll();
              Topic::importAll();
              WebPage::importAll();
              CodeBlock::importAll();
              Setting::importAll();
              FacebookImage::importAll();
              VideoType::importAll();
              YoutubeVideo::importAll();
              Post::importAll();

              Client::importAll();
              Survey::importAll();
              OnlineClient::importAll();
              GuestEmail::importAll();
              SentEmail::importAll();
              User::importAll();
              Permission::importAll();

              PostImage::importAll();
              PostPart::importAll();
              PostTopic::importAll();
              PostVideo::importAll();
              PostSetting::importAll();

              WebPageImage::importAll();
              WebPageVideo::importAll();
              WebPageSetting::importAll();

              //Important: This model has data from all models which have Translatable: Email, Post, Setting, Toping, WebPage
              Translation::importAll();

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.imported')]);
          }

          public function getImportNewPosts()
          {
              $canImport = true &&
              Topic::excelFileExists(true) &&
              Post::excelFileExists(true) &&
              PostTopic::excelFileExists(true) &&
              Translation::excelFileExists(true);

              if (! $canImport)
              {
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::errors.import_excel_files',['files' =>'Topic, Post, PostTopic, Translation'])]);
              }


              @set_time_limit($GLOBALS['CIOINA_Config']->get('PhpExecutionInMinutes') * 60);

              Topic::importAll(true);
              Post::importAll(true);
              PostTopic::importAll(true);
              //Important: This model has data from all models which have Translatable: Email, Post, Setting, Toping, WebPage
              Translation::importAll(true);

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.imported')]);
          }

          public function getSynchronize(Registrar $router)
          {
              $routes = $router->getRoutes();

              foreach ($routes->getRoutes() as $controller)
              {
                  $actions = $controller->getAction();

                  if (! empty($actions['controller']))
                  {
                      $service = $actions['controller'];
                      $serviceObject = $this->model->getByAction($service);

                      if ($serviceObject->isEmpty())
                      {
                          $model = new $this->model;
                          $model->action = $service;
                          $model->save();
                      }
                  }
              }

              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.synchronize')]);
          }
      }