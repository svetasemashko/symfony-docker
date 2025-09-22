<?php

namespace App\Tests\Service;

use App\Entity\ProductData;
use App\Service\ProductImportService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ProductImportServiceTest extends TestCase
{
    private string $csvPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->csvPath = sys_get_temp_dir() . '/test_products.csv';

        $content = implode("\n", [
            'Product Name,Product Description,Product Code,Cost in GBP,Stock,Discontinued',
            'Normal Prod,A nice product,NORMAL,10,20,false',
            'Cheap Prod,Too cheap,CHEAP,2,5,false',
            'Expensive,Too costly,EXP,2000,100,false',
            'Disco Prod,Old product,DISCONT,50,20,true',
        ]);

        new Filesystem()->dumpFile($this->csvPath, $content);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        @unlink($this->csvPath);
    }

    public function testImportFromFileProcessesAndPersistsCorrectly(): void
    {
        $persisted = [];
        $em = $this->createMock(EntityManagerInterface::class);

        $em->method('persist')
            ->willReturnCallback(function (ProductData $p) use (&$persisted) {
                $persisted[] = $p;
            });

        $em
            ->expects($this->once())
            ->method('flush');

        $service = new ProductImportService($em);

        $result = $service->importFromFile($this->csvPath);

        $this->assertSame(4, $result['processed']);
        $this->assertSame(2, $result['successful']);
        $this->assertSame(2, $result['skipped']);
        $this->assertSame([], $result['failed']);

        $this->assertCount(2, $persisted);

        $first = $persisted[0];
        $this->assertSame('NORMAL', $first->getCode());
        $this->assertSame(10.0, (float) $first->getPrice());
        $this->assertSame(20, (int) $first->getStockLevel());
        $this->assertNull($first->getDiscontinued());

        $second = $persisted[1];
        $this->assertSame('DISCONT', $second->getCode());
        $this->assertSame(50.0, (float) $second->getPrice());
        $this->assertSame(20, (int) $second->getStockLevel());
        $this->assertInstanceOf(\DateTime::class, $second->getDiscontinued());
    }

    public function testImportFromFileTestModeDoesNotPersistOrFlush(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);

        $em->expects($this->never())->method('persist');
        $em->expects($this->never())->method('flush');

        $service = new ProductImportService($em);
        $result = $service->importFromFile($this->csvPath, true);

        $this->assertSame(4, $result['processed']);
        $this->assertSame(2, $result['successful']);
        $this->assertSame(2, $result['skipped']);
        $this->assertSame([], $result['failed']);
    }
}
