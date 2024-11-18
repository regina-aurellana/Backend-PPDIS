<?php

namespace App\Http\Services\Files;

use Carbon\Carbon;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportService 
{

    public function export(array $data)
    {
        $writer = SimpleExcelWriter::streamDownload('Export.csv');

        $counter = 0;

        
        foreach ($data AS $values) {
            
            $row = [];
            
            foreach($values AS $key => $value)
            {
                $row[$key] = $value;
            }
            
            $writer->addRow($row);
            $counter++;
            
            if ($counter % 1000 === 0) {
                flush();
            }
        }

       return $writer;
        
    }

}