<?php namespace Acioina\UserManagement\Menu;

      class MenuConfig {

          public static function menu($merge = [], $direction = 'end')
          {
              $first = [
                  'left'  => [
                      [
                          'icon'    => 'home',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'slug'    => 'home',
                          'label'   => 'user-management::menu.home_page',
                      ],
                      [
                          'icon'    => 'comment',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\PostController@getIndex',
                          'label'   => 'user-management::menu.blog',
                      ],
                      [
                          'icon'    => 'eye-open',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\FacebookImageController@getIndex',
                          'label'   => 'user-management::menu.image',
                      ],
                      [
                          'icon'    => 'user',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'label'   => 'user-management::menu.user_account',
                          'submenu' => 
                           [
                                  [
                                      'icon'    => 'log-in',
                                      'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                                      'label'   => 'user-management::menu.client_login',
                                      'slug'    => 'login',
                                  ],
                                  [
                                       'icon'    => 'ok',
                                       'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                                       'label'   => 'user-management::menu.facebook_login',
                                       'slug'    => 'facebook-login',
                                   ],
                                   [
                                       'icon'    => 'remove',
                                       'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                                       'label'   => 'user-management::menu.facebook_logout',
                                       'slug'    => 'facebook-logout',
                                   ],
                                   [
                                       'icon'    => 'info-sign',
                                       'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                                       'label'   => 'user-management::menu.facebook_status',
                                       'slug'    => 'facebook-status',
                                   ],
                                   [
                                       'icon'    => 'envelope',
                                       'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                                       'label'   => 'user-management::menu.facebook_email',
                                       'slug'    => 'facebook-email',
                                   ],
                                   [
                                       'icon'    => 'list-alt',
                                       'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                                       'label'   => 'user-management::menu.client_registration',
                                       'slug'    => 'registration',
                                   ],
                             ],
                      ],
                      [
                          'icon'    => 'filter',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getIndex',
                          'label'   => 'user-management::menu.search_pages',
                      ],
                      [
                          'icon'    => 'question-sign',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'slug'    => 'faq',
                          'label'   => 'user-management::menu.faq_page',
                      ],
                      [
                          'icon'    => 'folder-close',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'slug'    => 'gdpr',
                          'label'   => 'user-management::menu.data_protection_page',
                      ],
                      [
                          'icon'    => 'envelope',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'slug'    => 'contact',
                          'label'   => 'user-management::menu.contact_page',
                      ],

                   ],
        'tasks' => [
                      [
                          'icon'    => 'log-in',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'label'   => 'user-management::menu.client_login',
                          'slug'    => 'login',
                      ],
                      [
                          'icon'    => 'list-alt',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'label'   => 'user-management::menu.client_registration',
                          'slug'    => 'registration',
                      ],
                      [
                          'icon'    => 'info-sign',
                          'action'  => '\Acioina\UserManagement\Http\Controllers\WebPageController@getView',
                          'label'   => 'user-management::menu.facebook_status',
                          'slug'    => 'facebook-status',
                      ],
                  ],

               ];

              if ($direction == 'end')
              {
                  $first['left']  = !empty($merge['left']) ? array_merge($first['left'], $merge['left']) : $first['left'];
                  $first['tasks'] = !empty($merge['tasks']) ? array_merge($first['tasks'], $merge['tasks']) : $first['tasks'];
              } else
              {
                  $first['left']  = !empty($merge['left']) ? array_merge($merge['left'], $first['left']) : $first['left'];
                  $first['tasks'] = !empty($merge['tasks']) ? array_merge($merge['tasks'], $first['tasks']) : $first['tasks'];
              }

              return $first;
          }
      }