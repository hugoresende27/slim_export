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
        $directory = '/var/www/slim_app/storage/converted/';
        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }


        $uploadedFile = $uploadedFiles['file'] ?? null;

        if (!$uploadedFile || $uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write('File error or not found');
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $jsonContent = json_decode($uploadedFile->getStream()->getContents(), true);

        if (!$jsonContent || !isset($jsonContent[0])) {
            $response->getBody()->write('Invalid JSON content');
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $lineCount = 2;

        foreach ($jsonContent[0] as $record) {
            $letterArrayPosition = 0;
            foreach ($record as $key => $value) {
                $sheet->setCellValue($columnLetters[$letterArrayPosition].'1', $key);
                $sheet->setCellValue($columnLetters[$letterArrayPosition].$lineCount, $value);
                $letterArrayPosition = ($letterArrayPosition + 1) % 10;
            }
            $lineCount++;
        }

        $filename = $directory.'converted.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $response->getBody()->write('File saved in: '.$filename. '---' . json_encode($jsonContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }






}