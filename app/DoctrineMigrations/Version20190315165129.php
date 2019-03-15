<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190315165129 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey_notification DROP FOREIGN KEY FK_CF867A9A45F6A4BB');
        $this->addSql('CREATE TABLE survey_notification_collection (id INT AUTO_INCREMENT NOT NULL, survey_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, time_of_notification DATETIME NOT NULL, notification_type INT NOT NULL, all_sent TINYINT(1) NOT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, sms_message VARCHAR(255) NOT NULL, email_subject VARCHAR(255) NOT NULL, email_message LONGTEXT NOT NULL, userGroup_id INT DEFAULT NULL, INDEX IDX_F31C05952B674466 (userGroup_id), INDEX IDX_F31C0595B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_notification_collection ADD CONSTRAINT FK_F31C05952B674466 FOREIGN KEY (userGroup_id) REFERENCES usergroup (id)');
        $this->addSql('ALTER TABLE survey_notification_collection ADD CONSTRAINT FK_F31C0595B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('DROP TABLE survey_notifier');
        $this->addSql('ALTER TABLE user_group_collection DROP number_of_total_users');
        $this->addSql('DROP INDEX IDX_CF867A9A45F6A4BB ON survey_notification');
        $this->addSql('ALTER TABLE survey_notification CHANGE surveynotifier_id surveyNotificationCollection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_notification ADD CONSTRAINT FK_CF867A9ADF059805 FOREIGN KEY (surveyNotificationCollection_id) REFERENCES survey_notification_collection (id)');
        $this->addSql('CREATE INDEX IDX_CF867A9ADF059805 ON survey_notification (surveyNotificationCollection_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey_notification DROP FOREIGN KEY FK_CF867A9ADF059805');
        $this->addSql('CREATE TABLE survey_notifier (id INT AUTO_INCREMENT NOT NULL, survey_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, time_of_notification DATETIME NOT NULL, notification_type INT NOT NULL, all_sent TINYINT(1) NOT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, sms_message VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, email_subject VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, email_message LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, userGroup_id INT DEFAULT NULL, INDEX IDX_7A119AA2B3FE509D (survey_id), INDEX IDX_7A119AA22B674466 (userGroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE survey_notifier ADD CONSTRAINT FK_7A119AA22B674466 FOREIGN KEY (userGroup_id) REFERENCES usergroup (id)');
        $this->addSql('ALTER TABLE survey_notifier ADD CONSTRAINT FK_7A119AA2B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('DROP TABLE survey_notification_collection');
        $this->addSql('DROP INDEX IDX_CF867A9ADF059805 ON survey_notification');
        $this->addSql('ALTER TABLE survey_notification CHANGE surveynotificationcollection_id surveyNotifier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_notification ADD CONSTRAINT FK_CF867A9A45F6A4BB FOREIGN KEY (surveyNotifier_id) REFERENCES survey_notifier (id)');
        $this->addSql('CREATE INDEX IDX_CF867A9A45F6A4BB ON survey_notification (surveyNotifier_id)');
        $this->addSql('ALTER TABLE user_group_collection ADD number_of_total_users INT DEFAULT NULL');
    }
}
