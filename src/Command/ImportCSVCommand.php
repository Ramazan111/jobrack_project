<?php

namespace App\Command;

use App\Service\PageCsvImporterService;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCSVCommand extends Command
{
    public function __construct(PageCsvImporterService $pageCsvImporterService)
    {
        $this->pageCsvImporterService = $pageCsvImporterService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName("app:import")
            ->setDescription("Import CSV content to Page table")
            ->addArgument("name", InputArgument::REQUIRED, "csv name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $response = $this->pageCsvImporterService->importCSV($input->getArgument('name'));

        if ($response['Code'] == 200) {
            $io->success($response['Message']);
            return Command::SUCCESS;
        } else {
            $io->error("Failed due to: " . $response['Message']);
            return Command::FAILURE;
        }
    }
}