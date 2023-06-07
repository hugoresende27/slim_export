<?php

namespace Http\Controllers;

use config\Db;
use PDO;
use PDOException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ToolsController
{

    public function jsonToxlsx(Response $response, Request $request)
    {
        $uploadedFiles = $request->getUploadedFiles();


        $directory = '/var/www/slim_app/storage/';

        if (!empty($uploadedFiles['file'])) {
            $uploadedFile = $uploadedFiles['file'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                // Read the JSON content
                $jsonContent = json_decode($uploadedFile->getStream()->getContents(), true);

                // Create a new spreadsheet
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

//                dd($jsonContent);
                $l = ['A','B','C','D','E','F','G','H','I','J'];
                $i = 0;
                foreach ($jsonContent[0] as $record)
                {
                    foreach ($record as $k => $r)
                    {
                        var_dump($l[$i]);
                        $sheet->setCellValue($l[$i].'1', $k);
                        if ($i == 9) {
                            break;
                        }
                        $i++;
                    }
                    if ($i == 9) {
                        break;
                    }
                }

//                // Save the spreadsheet to a file
                $filename = $directory.'converted.xlsx';
                $writer = new Xlsx($spreadsheet);
                $writer->save($filename);


                $response->getBody()->write('FILE SAVED IN : '.$filename);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }
            else {
                $response->getBody()->write('file error');
                return $response
                    ->withHeader('content-type','application/json')
                    ->withStatus(500);
            }
        }
        else {
            $response->getBody()->write('file not found');
            return $response
                ->withHeader('content-type','application/json')
                ->withStatus(500);
        }
    }




}