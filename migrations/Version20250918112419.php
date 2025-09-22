<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250918112419 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE tblProductData
    ADD decPrice NUMERIC(10, 2) NOT NULL DEFAULT 0.00,
    ADD intStockLevel INT NOT NULL DEFAULT 0'
        );
        $this->addSql('ALTER TABLE tblProductData RENAME INDEX strproductcode TO UNIQ_2C11248662F10A58');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tblProductData DROP decPrice, DROP intStockLevel');
        $this->addSql('ALTER TABLE tblProductData RENAME INDEX uniq_2c11248662f10a58 TO strProductCode');
    }
}
