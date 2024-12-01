<?php

return [
    'manager_root_dir'      => 'filemanager',
    'get_area_top_records'  => 30,
    'login_uri'             => $GLOBALS['CIOINA_Config']->get('LaravelAdminUri').'/login',
    'admin_base_uri'        => $GLOBALS['CIOINA_Config']->get('LaravelAdminUri'),
    'blog_view_uri'         => '/blog/view/',

    'logout_action'                 => 'Distilleries\Expendable\Http\Controllers\Admin\LoginController@getLogout',
    'reset_password_action'         => 'Distilleries\Expendable\Http\Controllers\Admin\LoginController@getReset',
    'post_reset_password_action'    => 'Distilleries\Expendable\Http\Controllers\Admin\LoginController@postReset',
    'reset_admin_password'          => false,

    'listener'            => [
        '\Distilleries\Expendable\Listeners\UserListener'
    ],
    'mail'                => [
        'actions' => [
            'emails.password'
        ]
    ],
    'menu'                => \Distilleries\Expendable\Config\MenuConfig::menu([], 'beginning'),
    'menu_left_collapsed' => false,
    'state'               => [
        'Distilleries\DatatableBuilder\Contracts\DatatableStateContract' => [
            'color'    => 'bg-green-haze',
            'icon'     => 'th-list',
            'label'    => 'expendable::menu.datatable',
            'position' => 0,
            'action'   => 'getIndex'
        ],
        'Distilleries\Expendable\Contracts\ExportStateContract'          => [
            'color'    => 'bg-blue-hoki',
            'icon'     => 'save-file',
            'label'    => 'expendable::menu.export',
            'position' => 1,
            'action'   => 'getExport'
        ],
        'Distilleries\Expendable\Contracts\ImportStateContract'          => [
            'color'    => 'bg-red-sunglo',
            'icon'     => 'open-file',
            'label'    => 'expendable::menu.import',
            'position' => 2,
            'action'   => 'getImport'
        ],
        'Distilleries\FormBuilder\Contracts\FormStateContract'           => [
            'color'    => 'bg-yellow',
            'icon'     => 'pencil',
            'label'    => 'expendable::menu.add_state',
            'position' => 3,
            'action'   => 'getEdit'
        ],
    ]
];