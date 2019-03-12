<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190312205842 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_link_click (id INT AUTO_INCREMENT NOT NULL, notification_id INT DEFAULT NULL, timeOfVisit DATETIME NOT NULL, INDEX IDX_503794C9EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_link_click ADD CONSTRAINT FK_503794C9EF1A9D84 FOREIGN KEY (notification_id) REFERENCES survey_notification (id)');
        $this->addSql('ALTER TABLE usergroup_collection CHANGE isactive isDeletable TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE survey_notifier ADD senderUser_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_notifier ADD CONSTRAINT FK_7A119AA26F2B3395 FOREIGN KEY (senderUser_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7A119AA26F2B3395 ON survey_notifier (senderUser_id)');
        $this->addSql('ALTER TABLE survey_notification DROP timeOfFirstVisit');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE survey_link_click');
        $this->addSql('ALTER TABLE survey_notification ADD timeOfFirstVisit DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_notifier DROP FOREIGN KEY FK_7A119AA26F2B3395');
        $this->addSql('DROP INDEX IDX_7A119AA26F2B3395 ON survey_notifier');
        $this->addSql('ALTER TABLE survey_notifier DROP senderUser_id');
        $this->addSql('ALTER TABLE usergroup_collection CHANGE isdeletable isActive TINYINT(1) NOT NULL');
    }
}
