<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Support\Str;
      use \DB;

      trait BaseModel
      {

          public static function getChoice()
          {
              $data   = self::all();
              $result = [];
              foreach ($data as $item)
              {
                  $result[$item['id']] = isset($item['label']) ? $item['label'] : $item['id'];
              }

              return $result;
          }

          public function scopeSearch($query, $searchQuery)
          {
              return $query->where(function($query) use ($searchQuery)
              {
                  $columns = $this->getAllColumnsNames();

                  foreach ($columns as $column)
                  {
                      $query->orwhere($column, 'like', '%' . $searchQuery . '%');
                  }
              });
          }

          public function getAllColumnsNames()
          {
              return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
          }

          public function scopeBetweenCreate($query, $start, $end)
          {
              return $query->whereBetween($this->getTable() . '.created_at', array($start, $end));
          }

          public function scopeBetweenUpdate($query, $start, $end)
          {
              return $query->whereBetween($this->getTable() . '.updated_at', array($start, $end));
          }

          public static function getAllColumnsNamesStatic()
          {
              return with(new static)->getConnection()->getSchemaBuilder()->getColumnListing(self::getTableNameStatic());
          }

          public static function getTableNameStatic()
          {
              return with(new static)->getTable();
          }

          private static function getExportFile($addNewData = false)
          {
              $dataDir =  $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir');
              $tableName = self::getTableNameStatic();
              $managerRoot = config('expendable.manager_root_dir');

              if($addNewData)
              {
                  return $dataDir. DIRECTORY_SEPARATOR . $managerRoot. DIRECTORY_SEPARATOR . $GLOBALS['CIOINA_Config']->get('NewPostsDir') . DIRECTORY_SEPARATOR . $tableName;
              }else{
                  return $dataDir. DIRECTORY_SEPARATOR . $managerRoot. DIRECTORY_SEPARATOR . $tableName;
              }
          }

          public static function excelFileExists($addNewData = false)
          {
              $file = self::getExportFile($addNewData);
              return  (\File::exists($file . '.xls') || \File::exists($file . '.csv'));
          }

          public static function exportAll()
          {
              $tableName = self::getTableNameStatic();

              $file = self::getExportFile();

              if (\File::exists($file . '.xls') )
              {
                  return;
              }

              $exporter = app('Distilleries\Expendable\Contracts\ExcelExporterContract');
              $result = DB::table($tableName)->select(self::getAllColumnsNamesStatic())->get();
              if (! empty($result) && count($result) > 0)
              {
                  $result = collect($result)->map(
                      function($x)
                      {
                          return (array) $x;
                      })->toArray();

                  $exporter->export($result, $tableName);
              }
          }

          public static function truncateAll()
          {
              $tableName = self::getTableNameStatic();
              DB::table($tableName)->truncate();

              if(config('database.default') === 'testbench')
              {
                  DB::update("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = '$tableName'");
              }else{
                  DB::update("ALTER TABLE $tableName AUTO_INCREMENT = 0;");
              }
          }

          public static function importAll($addNewData = false)
          {
              $tableName = self::getTableNameStatic();

              $file = self::getExportFile($addNewData);

              if(config('database.default') === 'testbench')
              {
                  DB::update("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = '$tableName'");
              }else{
                  DB::update("ALTER TABLE $tableName AUTO_INCREMENT = 0;");
              }

              if (app('files')->exists($file . '.xls'))
              {
                  $file .= '.xls';
                  $exporter = app('XlsImporterContract');
              }elseif(app('files')->exists($file . '.csv')){
                  $file .= '.csv';
                  $exporter = app('CsvImporterContract');
              }else{
                  return;
              }

              $data = $exporter->getArrayDataFromFile($file);

              $auto_id = 0;
              foreach ($data as $item)
              {
                  foreach ($item as $key=> $val)
                  {
                      if (isset($item['id']))
                      {
                          if($auto_id < $item['id'])
                          {
                              $auto_id = $item['id'];
                          }
                      }

                      switch($key) {
                          case 'cache_key':
                              if(empty($val))
                              {
                                  $item[$key] = Str::uuid();
                              }
                              break;
                          case 'status':
                              if(empty($val))
                              {
                                  $item[$key] = 0;
                              }
                              break;
                          case 'created_at':
                          case 'updated_at':
                              if(!empty($val))
                              {
                                  $item[$key] = \DateTime::createFromFormat('Y-m-d H:i:s', $val);
                              }
                              break;
                          default:
                              break;
                      }

                      switch($tableName)
                      {
                          case 'surveys':
                              switch($key) {
                                  case 'is_complex_name':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  case 'session_id':
                                      if(empty($val))
                                      {
                                          $item[$key] = 'NONE';
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'online_clients':
                              switch($key) {
                                  case 'is_logged_out':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'clients':
                              switch($key) {
                                  case 'fb_verified':
                                  case 'is_suspended':
                                  case 'is_deleted':
                                  case 'is_remember_username':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'guest_emails':
                              switch($key) {
                                  case 'has_facebook':
                                  case 'is_facebook':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'us_states':
                              switch($key) {
                                  case 'is_territory':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'translations':
                              switch($key) {
                                  case 'id_source':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'web_pages':
                              switch($key) {
                                  case 'has_form':
                                  case 'is_public':
                                  case 'is_raw':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'posts':
                              switch($key) {
                                  case 'facebook_image_id':
                                  case 'is_raw':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'emails':
                              switch($key) {
                                  case 'cc':
                                  case 'bcc':
                                      if(empty($val))
                                      {
                                          $item[$key] = '';
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'languages':
                              switch($key) {
                                  case 'not_visible':
                                  case 'is_default':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'roles':
                              switch($key) {
                                  case 'overide_permission':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'facebook_images':
                              switch($key) {
                                  case 'is_expired':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'video_types':
                              switch($key) {
                                  case 'video_type_id':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;
                          case 'youtube_videos':
                              switch($key) {
                                  case 'video_type':
                                      if(empty($val))
                                      {
                                          $item[$key] = 0;
                                      }
                                      break;
                                  default:
                                      break;
                              }
                              break;

                          default:
                              break;
                      }

                  }
                  DB::table($tableName)->insert($item);
              }

              if ($auto_id > 0)
              {
                  $auto_id++;

                  if(config('database.default') === 'testbench')
                  {
                      DB::update("UPDATE SQLITE_SEQUENCE SET seq = $auto_id WHERE name = '$tableName'");
                  }else{
                      DB::update("ALTER TABLE $tableName AUTO_INCREMENT = $auto_id;");
                  }
              }

          }

      }