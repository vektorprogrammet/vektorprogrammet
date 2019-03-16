<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190316111959 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE surveynotificationcollection_usergroup (surveynotificationcollection_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_A0F43A96DC559D2C (surveynotificationcollection_id), INDEX IDX_A0F43A96D2112630 (usergroup_id), PRIMARY KEY(surveynotificationcollection_id, usergroup_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE surveynotificationcollection_usergroup ADD CONSTRAINT FK_A0F43A96DC559D2C FOREIGN KEY (surveynotificationcollection_id) REFERENCES survey_notification_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE surveynotificationcollection_usergroup ADD CONSTRAINT FK_A0F43A96D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_notification_collection DROP FOREIGN KEY FK_F31C05952B674466');
        $this->addSql('DROP INDEX IDX_F31C05952B674466 ON survey_notification_collection');
        $this->addSql('ALTER TABLE survey_notification_collection ADD email_end_message LONGTEXT NOT NULL, ADD email_type INT NOT NULL, DROP userGroup_id');
        $this->addSql('ALTER TABLE survey DROP showCustomFinishPage, CHANGE finishPageContent finishPageContent LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE surveynotificationcollection_usergroup');
        $this->addSql('ALTER TABLE survey ADD showCustomFinishPage TINYINT(1) NOT NULL, CHANGE finishPageContent finishPageContent LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE survey_notification_collection ADD userGroup_id INT DEFAULT NULL, DROP email_end_message, DROP email_type');
        $this->addSql('ALTER TABLE survey_notification_collection ADD CONSTRAINT FK_F31C05952B674466 FOREIGN KEY (userGroup_id) REFERENCES usergroup (id)');
        $this->addSql('CREATE INDEX IDX_F31C05952B674466 ON survey_notification_collection (userGroup_id)');
    }
}
