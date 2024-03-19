<?php namespace Distilleries\Expendable\Exporter;

      use Distilleries\Expendable\Contracts\CsvExporterContract;

      class CsvExporter implements CsvExporterContract 
      {
          public function export($data, $filename = '')
          {
              $excel = \Excel::create($filename, 
                  function($excel) use($data) 
                  {
                      $excel->sheet('export', 
                          function($sheet) use($data) 
                          {
                              $sheet->fromArray($data);
                          });
                  });

              // storage_path('exports') for unit testing only
              $excel->store('csv', $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') === false ? storage_path('exports'): 
                  $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') . DIRECTORY_SEPARATOR . config('expendable.manager_root_dir') );
          }
      }