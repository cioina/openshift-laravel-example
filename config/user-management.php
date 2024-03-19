<?php
use Distilleries\Expendable\Helpers\Jsv4;

$query = 'SELECT '
.\CIOINA_Util::backquote('id') .','
. \CIOINA_Util::backquote('code_block') 
. ' FROM ' . \CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
. '.' . \CIOINA_Util::backquote('settings')
. ' WHERE ' . \CIOINA_Util::backquote('id') . '= 60'
. ' OR ' . \CIOINA_Util::backquote('id') . '= 61'
. ' ORDER BY ' . \CIOINA_Util::backquote('id');
$records = $GLOBALS['CIOINA_dbi']->fetchResult($query);

if (isset($records) && count($records) === 2)
{
    $fields = json_decode('{' . $records[1]['code_block'] . '}', true);
    if( count($fields) > 0 )
    {
        $result = Jsv4::isValidMember(json_decode('{' . $records[1]['code_block'] . '}'), json_decode('{' . $records[0]['code_block'] . '}'), 'left');
        if( !isset($result) || $result)
        {
            foreach ($fields[ 'left'] as $key => $value)
            {
                $fields[ 'left'][$key]['action'] = str_replace('/', '\\', $fields[ 'left'][$key]['action']);
            }
        }else{
            unset($fields);
        }

    }else{
        unset($fields);
    }
}

unset($query, $records, $result);

return [
    'blog_view_uri'       => '/blog/view/',
    'home_page'           => '/',
    'menu'                => \Acioina\UserManagement\Menu\MenuConfig::menu(isset($fields) ? $fields : [], 'end'),
    'menu_left_collapsed' => false,
    'listener'            => [
        '\Distilleries\Expendable\Listeners\UserListener'
    ],
    'mail'                => [
        'actions' => [
            'emails.password'
        ]
    ],
    'state'               => [
        'Acioina\UserManagement\Contracts\DatatableStateContract' => [
            'color'    => 'bg-green-haze',
            'icon'     => 'th-list',
            'label'  => 'user-management::menu.datatable',
            'position' => 0,
            'action'   => 'getIndex'
        ],
     ]
    
];