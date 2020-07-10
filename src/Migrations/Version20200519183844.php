<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519183844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDriver()->getName() !== 'pdo_mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("drop table tags;");
        $this->addSql("alter table product_tags add related_type int not null;");
        $this->addSql("update product_tags set related_type = 1;");
        $this->addSql("rename table product_tags to tags;");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
