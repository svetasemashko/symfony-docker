<?php

namespace App\Command;

use App\Service\ProductImportService;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Exception;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from file into DB',
)]
class ImportProductsCommand extends Command
{
    public function __construct(private readonly ProductImportService $productImportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to CSV file')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Run in test mode (do not persist)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $isTest   = (bool) $input->getOption('test');

        $output->writeln(
            sprintf(
                'Starting import from the file: %s. %s',
                $filePath,
                $isTest ? 'Running in test mode' : '',
            ),
        );

        try {
            $result = $this->productImportService->importFromFile($filePath, $isTest);

            $output->writeln('Processed: ' . $result['processed']);
            $output->writeln('Successful: ' . $result['successful']);
            $output->writeln('Skipped: ' . $result['skipped']);

            if (!empty($result['failed'])) {
                $output->writeln('Failed items:');

                foreach ($result['failed'] as $failed) {
                    $output->writeln(json_encode($failed));
                }
            }
        } catch (InvalidArgumentException|Exception $e) {
            $output->writeln('Import failed: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln('');
        $output->writeln('Import finished');

        return Command::SUCCESS;
    }
}
