<?php

namespace Acioina\UserManagement\Console\Commands;

use Illuminate\Console\Command;
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


use Illuminate\Contracts\Console\Kernel;


class ImportAllCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user-management:importAllExcelFiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all Excel files';

    protected $artisan;

    public function __construct(Kernel $artisan)
    {
        parent::__construct();

        $this->artisan   = $artisan;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
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
            $this->error('Cannot import Excel files');
            return;
        }

        @set_time_limit($GLOBALS['CIOINA_Config']->get('PhpExecutionInMinutes') * 60);

        $this->importAllExcelFiles();
        //$this->dropAllTables();

        $this->info('All Excel files imported!');
    }

    private function importAllExcelFiles()
    {
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
    }

    private function dropAllTables()
    {
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
    }

}
