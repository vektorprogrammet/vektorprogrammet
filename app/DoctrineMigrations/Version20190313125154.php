<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313125154 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE usergroupcollection_user (usergroupcollection_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D5AF28D8D3EE73A6 (usergroupcollection_id), INDEX IDX_D5AF28D8A76ED395 (user_id), PRIMARY KEY(usergroupcollection_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usergroupcollection_user ADD CONSTRAINT FK_D5AF28D8D3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES usergroup_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_user ADD CONSTRAINT FK_D5AF28D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_notifier ADD smsMessage VARCHAR(255) NOT NULL, ADD emailSubject VARCHAR(255) NOT NULL, ADD emailMessage LONGTEXT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF867A9A750FAC43 ON survey_notification (userIdentifier)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE usergroupcollection_user');
        $this->addSql('DROP INDEX UNIQ_CF867A9A750FAC43 ON survey_notification');
        $this->addSql('ALTER TABLE survey_notifier DROP smsMessage, DROP emailSubject, DROP emailMessage');
    }
}
