<?php namespace Distilleries\Expendable\Config;

      class MenuConfig {

          public static function menu($merge = [], $direction = 'end')
          {

              $first = [
                  'left'  => [
                   [
                       'icon'    => 'trash',
                       'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\WebStatisticsController@getIndex',
                       'label'   => 'expendable::menu.web_statistics',
                       'submenu' => [
                           [
                               'icon'    => 'th-list',
                               'label'   => 'expendable::menu.list_of',
                               'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\WebStatisticsController@getIndex',
                           ],
                           [
                               'icon'    => 'pencil',
                               'label'   => 'expendable::menu.add',
                               'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\WebStatisticsController@getEdit',
                           ],
                       ],
                  ],
                  [
                       'icon'    => 'question-sign',
                       'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\SettingController@getIndex',
                       'label'   => 'expendable::menu.setting',
                       'submenu' => [
                           [
                               'icon'    => 'th-list',
                               'label'   => 'expendable::menu.list_of',
                               'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\SettingController@getIndex',
                           ],
                           [
                               'icon'    => 'pencil',
                               'label'   => 'expendable::menu.add',
                               'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\SettingController@getEdit',
                           ],
                       ],
                  ],
                  [
                          'icon'    => 'globe',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\WebPageController@getIndex',
                          'label'   => 'expendable::menu.web_page',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\WebPageController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\WebPageController@getEdit',
                              ],
                          ],
                   ],
                   [
                          'icon'    => 'cloud',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\TopicController@getIndex',
                          'label'   => 'expendable::menu.topic',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\TopicController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\TopicController@getEdit',
                              ],
                          ],
                   ],
                   [
                          'icon'    => 'comment',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex',
                          'label'   => 'expendable::menu.post',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\PostController@getEdit',
                              ],
                          ],
                      ],
                      [
                          'icon'            => 'eye-open',
                          'action'          => '\Distilleries\Expendable\Http\Controllers\Admin\FacebookImageController@getIndex',
                          'label'           => 'expendable::menu.facebook_image',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\FacebookImageController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\FacebookImageController@getEdit',
                              ],
                              [
                                  'icon'    => 'refresh',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\FacebookImageController@getSynchronize',
                                  'label'   => 'expendable::menu.sync_images',
                              ],

                          ],
                      ],
                      [
                          'icon'    => 'hd-video',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\YoutubeVideoController@getIndex',
                          'label'   => 'expendable::menu.youtube_video',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\YoutubeVideoController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\YoutubeVideoController@getEdit',
                              ],
                          ],
                      ],
                      [
                          'icon'    => 'facetime-video',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\VideoTypeController@getIndex',
                          'label'   => 'expendable::menu.video_type',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\VideoTypeController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\VideoTypeController@getEdit',
                              ],
                          ],
                      ],
                      [
                          'icon'    => 'blackboard',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\CodeBlockController@getIndex',
                          'label'   => 'expendable::menu.code_block',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\CodeBlockController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\CodeBlockController@getEdit',
                              ],
                          ],
                      ],
                      [
                          'icon'    => 'send',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\EmailTypeController@getIndex',
                          'label'   => 'expendable::menu.email_type',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\EmailTypeController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\EmailTypeController@getEdit',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.sent_emails',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\SentEmailController@getIndex',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.guest_emails',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\GuestEmailController@getIndex',
                              ],
                          ],
                     ],
                     [
                          'icon'    => 'envelope',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\EmailController@getIndex',
                          'label'   => 'expendable::menu.email',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\EmailController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\EmailController@getEdit',
                              ],
                          ],
                      ],
                      [
                          'icon'    => 'user',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\UserController@getIndex',
                          'label'   => 'expendable::menu.user',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\UserController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\UserController@getEdit',
                              ],
                              [
                                  'icon'    => 'user',
                                  'label'   => 'expendable::menu.my_profile',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\UserController@getProfile',
                              ],

                          ],
                      ],
                      [
                          'icon'    => 'paperclip',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\RoleController@getIndex',
                          'label'   => 'expendable::menu.role',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\RoleController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\RoleController@getEdit',
                              ],

                          ],
                      ],
                      [
                          'icon'    => 'tags',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getIndex',
                          'label'   => 'expendable::menu.service',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getEdit',
                              ],
                              [
                                  'icon'    => 'retweet',
                                  'label'   => 'expendable::menu.sync_service',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getSynchronize',
                              ],
                              [
                                  'icon'    => 'export',
                                  'label'   => 'expendable::menu.export_service',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getExportAllData',
                              ],
                              [
                                  'icon'    => 'import',
                                  'label'   => 'expendable::menu.import_service',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getImportAllData',
                              ],
                               [
                                   'icon'    => 'comment',
                                   'label'   => 'expendable::menu.import_new_posts',
                                   'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getImportNewPosts',
                               ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.associate_permission',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\PermissionController@getIndex',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.us_states',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\UsStateController@getIndex',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.countries',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\CountryController@getIndex',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.clients',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ClientController@getIndex',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.online_clients',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\OnlineClientController@getIndex',
                              ],
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.surveys',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\SurveyController@getIndex',
                              ],

                          ],
                      ],
                      [
                          'icon'    => 'flag',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\LanguageController@getIndex',
                          'label'   => 'expendable::menu.language',
                          'submenu' => [
                              [
                                  'icon'    => 'th-list',
                                  'label'   => 'expendable::menu.list_of',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\LanguageController@getIndex',
                              ],
                              [
                                  'icon'    => 'pencil',
                                  'label'   => 'expendable::menu.add',
                                  'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\LanguageController@getEdit',
                              ],

                          ],
                      ],
                  ],

                  'tasks' => [
                      [
                          'icon'    => 'console',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ComponentController@getIndex',
                          'label'   => 'expendable::menu.generate_component',

                      ],
                      [
                          'icon'    => 'retweet',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getSynchronize',
                          'label'   => 'expendable::menu.sync_service',

                      ],
                      [
                          'icon'    => 'refresh',
                          'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\FacebookImageController@getSynchronize',
                          'label'   => 'expendable::menu.sync_images',

                      ],
                      [
                           'icon'    => 'export',
                           'label'   => 'expendable::menu.export_service',
                           'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getExportAllData',
                       ],
                       [
                           'icon'    => 'import',
                           'label'   => 'expendable::menu.import_service',
                           'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getImportAllData',
                       ],
                       [
                           'icon'    => 'comment',
                           'label'   => 'expendable::menu.import_new_posts',
                           'action'  => '\Distilleries\Expendable\Http\Controllers\Admin\ServiceController@getImportNewPosts',
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