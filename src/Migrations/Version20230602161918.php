<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602161918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE `description` SET type = \'loyalty\' WHERE type = \'1\'');
        $this->addSql('UPDATE `description` SET type = \'policy_and_privacy\' WHERE type = \'2\'');
        $this->addSql('UPDATE `description` SET type = \'use_conditions\' WHERE type = \'3\'');
        $this->addSql('UPDATE `description` SET type = \'collaborator\' WHERE type = \'4\'');
        $this->addSql('UPDATE `description` SET type = \'career\' WHERE type = \'5\'');
        $this->addSql('UPDATE `description` SET type = \'cookie_policy\' WHERE type = \'6\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
