<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501064139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promotion ADD tags_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD18D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C11D7DD18D7B4FB4 ON promotion (tags_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promotion DROP FOREIGN KEY FK_C11D7DD18D7B4FB4');
        $this->addSql('DROP INDEX UNIQ_C11D7DD18D7B4FB4 ON promotion');
        $this->addSql('ALTER TABLE promotion DROP tags_id');
    }
}
