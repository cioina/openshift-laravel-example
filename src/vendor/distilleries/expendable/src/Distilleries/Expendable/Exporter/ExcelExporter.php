<?php

namespace Distilleries\Expendable\Exporter;

use Distilleries\Expendable\Contracts\ExcelExporterContract;

class ExcelExporter implements ExcelExporterContract 
{
    public function export($data, $filename = '')
    {
        $excel = \Excel::create($filename, 
        function($excel) use ($data)
        {
            $excel->sheet('export', 
                function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                    $sheet->freezeFirstRow();
                    $sheet->setAutoFilter();
                    $sheet->row(1, 
                        function($row)
                        {
                            $row->setValignment('middle');
                            $row->setAlignment('center');
                            $row->setFontColor('#ffffff');
                            $row->setFont(array(
                                'size' => '12',
                                'bold' => true
                            ));
                            $row->setBackground('#000000');
                        });
                });
        });

        // storage_path('exports') for unit testing only
        $excel->store('xls', $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') === false ? storage_path('exports'): 
            $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') . DIRECTORY_SEPARATOR . config('expendable.manager_root_dir') );

        //TODO: Delete this
        //if (app()->environment('testing'))
        //{
        //    $excel->store('xls');
        //}

        //$excel->export('xls')->download('xls');
    }
}