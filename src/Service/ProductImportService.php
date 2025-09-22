<?php

namespace App\Service;

use App\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

readonly class ProductImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductFactory $productFactory,
    )
    {
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     */
    public function importFromFile(string $filePath, bool $isTest = false): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException('File not found: ' . $filePath);
        }

        if (!is_readable($filePath)) {
            throw new InvalidArgumentException('File not readable: ' . $filePath);
        }

        $processed   = 0;
        $successful  = 0;
        $skipped     = 0;
        $failedItems = [];

        $reader = Reader::createFromPath($filePath);

        $reader->setHeaderOffset(0);

        $records = $reader->getRecords();

        $batchSize    = 20;
        $currentBatch = 0;

        foreach ($records as $record) {
            $processed++;

            $name        = trim($record['Product Name']) ?? null;
            $description = trim($record['Product Description']) ?? null;
            $code        = trim($record['Product Code']) ?? null;
            $price       = (float) ($record['Cost in GBP'] ?? 0);
            $stock       = (int) ($record['Stock'] ?? 0);

            if (
                $name === null
                || $code === null
                || ($price < 5 && $stock < 10)
                || $price > 1000
            ) {
                $skipped++;

                continue;
            }

            $product = $this->productFactory->createProduct(
                $name,
                $description,
                $code,
                $price,
                $stock,
                !empty($record['Discontinued']) && strtolower($record['Discontinued']) !== 'false',
            );

            if (!$isTest) {
                $this->entityManager->persist($product);

                $currentBatch++;

                if ($currentBatch >= $batchSize) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();

                    $currentBatch = 0;
                }
            }

            $successful++;
        }

        if (!$isTest && $currentBatch > 0) {
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return [
            'processed'  => $processed,
            'successful' => $successful,
            'skipped'    => $skipped,
            'failed'     => $failedItems,
        ];
    }
}
