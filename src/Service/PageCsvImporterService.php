<?php

namespace App\Service;

use App\Entity\Page;
use App\Repository\PageRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PageCsvImporterService
{
    private $pageRepository;

    private String $fileFormat = 'csv';

    public function __construct($importCsvDir, PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
        $this->importCsvDir = $importCsvDir;
    }

    /**
     * Finds all countries
     */
    public function findAll() {

        $data = $this->pageRepository->findAll();

        return $data;
    }

    public function readCSV($fileName)
    {
        $inputFile = $this->importCsvDir . $fileName . "." . $this->fileFormat;

        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $decoder->decode(file_get_contents($inputFile), $this->fileFormat);
    }

    public function importCSV($fileName)
    {
        try {
            $rows = $this->readCSV($fileName);
            $counter = 0;
            foreach ($rows as $row) {
                if (!filter_var($row['url'], FILTER_VALIDATE_URL) === false) {
                    $counter++;
                    $page = new Page();
                    $page->setUrl($row['url']);
                    $page->setCreatedAt(new \DateTime());
                    $this->pageRepository->save($page, true);
                }
            }
            return [
                "Code" => 200,
                "Message" => "There were added " . $counter . " successfully!"
            ];
        } catch (Exception $exception) {
            return [
                "Code" => $exception->getCode(),
                "Message" => $exception->getMessage()
            ];
        }
    }
}