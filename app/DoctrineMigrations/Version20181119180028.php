<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181119180028 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE reserved_pop_up reserved_pop_up TINYINT(1) NOT NULL, CHANGE last_pop_up last_pop_up DATETIME NOT NULL');
        $this->addSql('ALTER TABLE survey CHANGE createdTime createdTime DATETIME NOT NULL, CHANGE showCustomFinishPage showCustomFinishPage TINYINT(1) NOT NULL, CHANGE showCustomPopUpMessage showCustomPopUpMessage TINYINT(1) NOT NULL, CHANGE surveyPopUpMessage surveyPopUpMessage LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey CHANGE createdTime createdTime DATETIME DEFAULT NULL, CHANGE showCustomFinishPage showCustomFinishPage TINYINT(1) DEFAULT NULL, CHANGE showCustomPopUpMessage showCustomPopUpMessage TINYINT(1) DEFAULT NULL, CHANGE surveyPopUpMessage surveyPopUpMessage LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE reserved_pop_up reserved_pop_up TINYINT(1) DEFAULT NULL, CHANGE last_pop_up last_pop_up DATETIME DEFAULT NULL');
    }
}
