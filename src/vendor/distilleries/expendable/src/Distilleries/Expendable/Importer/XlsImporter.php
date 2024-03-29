<?php namespace Distilleries\Expendable\Importer;

      use Distilleries\Expendable\Contracts\XlsImporterContract;
      use \Excel;

      class XlsImporter implements XlsImporterContract 
      {
          public function getArrayDataFromFile($file)
          {
              $data = Excel::load($file, 'UTF-8');

              return $data->toArray();
          }
      }