<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190316205132 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_group_collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, number_of_user_groups INT NOT NULL, assistant_bolk LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', deletable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_team (usergroupcollection_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_9CDC588ED3EE73A6 (usergroupcollection_id), INDEX IDX_9CDC588E296CD8AE (team_id), PRIMARY KEY(usergroupcollection_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_semester (usergroupcollection_id INT NOT NULL, semester_id INT NOT NULL, INDEX IDX_5BDDF6CFD3EE73A6 (usergroupcollection_id), INDEX IDX_5BDDF6CF4A798B6F (semester_id), PRIMARY KEY(usergroupcollection_id, semester_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_user (usergroupcollection_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D5AF28D8D3EE73A6 (usergroupcollection_id), INDEX IDX_D5AF28D8A76ED395 (user_id), PRIMARY KEY(usergroupcollection_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_department (usergroupcollection_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_3471F241D3EE73A6 (usergroupcollection_id), INDEX IDX_3471F241AE80F5DF (department_id), PRIMARY KEY(usergroupcollection_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroup (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, userGroupCollection_id INT DEFAULT NULL, INDEX IDX_4A6478173DD68063 (userGroupCollection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroup_user (usergroup_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E9B5A1C0D2112630 (usergroup_id), INDEX IDX_E9B5A1C0A76ED395 (user_id), PRIMARY KEY(usergroup_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_notification_collection (id INT AUTO_INCREMENT NOT NULL, survey_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, time_of_notification DATETIME NOT NULL, notification_type INT NOT NULL, all_sent TINYINT(1) NOT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, sms_message VARCHAR(255) NOT NULL, email_from_name VARCHAR(255) NOT NULL, email_subject VARCHAR(255) NOT NULL, email_message LONGTEXT NOT NULL, email_end_message LONGTEXT NOT NULL, email_type INT NOT NULL, INDEX IDX_F31C0595B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE surveynotificationcollection_usergroup (surveynotificationcollection_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_A0F43A96DC559D2C (surveynotificationcollection_id), INDEX IDX_A0F43A96D2112630 (usergroup_id), PRIMARY KEY(surveynotificationcollection_id, usergroup_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_link_click (id INT AUTO_INCREMENT NOT NULL, notification_id INT DEFAULT NULL, time_of_visit DATETIME NOT NULL, INDEX IDX_503794C9EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, time_notification_Sent DATETIME DEFAULT NULL, user_identifier VARCHAR(255) NOT NULL, sent TINYINT(1) NOT NULL, surveyNotificationCollection_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_CF867A9AD0494586 (user_identifier), INDEX IDX_CF867A9AA76ED395 (user_id), INDEX IDX_CF867A9ADF059805 (surveyNotificationCollection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usergroupcollection_team ADD CONSTRAINT FK_9CDC588ED3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES user_group_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_team ADD CONSTRAINT FK_9CDC588E296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_semester ADD CONSTRAINT FK_5BDDF6CFD3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES user_group_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_semester ADD CONSTRAINT FK_5BDDF6CF4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_user ADD CONSTRAINT FK_D5AF28D8D3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES user_group_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_user ADD CONSTRAINT FK_D5AF28D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_department ADD CONSTRAINT FK_3471F241D3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES user_group_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_department ADD CONSTRAINT FK_3471F241AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroup ADD CONSTRAINT FK_4A6478173DD68063 FOREIGN KEY (userGroupCollection_id) REFERENCES user_group_collection (id)');
        $this->addSql('ALTER TABLE usergroup_user ADD CONSTRAINT FK_E9B5A1C0D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroup_user ADD CONSTRAINT FK_E9B5A1C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_notification_collection ADD CONSTRAINT FK_F31C0595B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE surveynotificationcollection_usergroup ADD CONSTRAINT FK_A0F43A96DC559D2C FOREIGN KEY (surveynotificationcollection_id) REFERENCES survey_notification_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE surveynotificationcollection_usergroup ADD CONSTRAINT FK_A0F43A96D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_link_click ADD CONSTRAINT FK_503794C9EF1A9D84 FOREIGN KEY (notification_id) REFERENCES survey_notification (id)');
        $this->addSql('ALTER TABLE survey_notification ADD CONSTRAINT FK_CF867A9AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE survey_notification ADD CONSTRAINT FK_CF867A9ADF059805 FOREIGN KEY (surveyNotificationCollection_id) REFERENCES survey_notification_collection (id)');

        $this->addSql('ALTER TABLE survey ADD targetAudience INT DEFAULT 0 NOT NULL');
        $this->addSql('UPDATE survey SET targetAudience=1 WHERE teamSurvey=\'1\'');
        $this->addSql('ALTER TABLE survey DROP teamSurvey, DROP showCustomFinishPage');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE usergroupcollection_team DROP FOREIGN KEY FK_9CDC588ED3EE73A6');
        $this->addSql('ALTER TABLE usergroupcollection_semester DROP FOREIGN KEY FK_5BDDF6CFD3EE73A6');
        $this->addSql('ALTER TABLE usergroupcollection_user DROP FOREIGN KEY FK_D5AF28D8D3EE73A6');
        $this->addSql('ALTER TABLE usergroupcollection_department DROP FOREIGN KEY FK_3471F241D3EE73A6');
        $this->addSql('ALTER TABLE usergroup DROP FOREIGN KEY FK_4A6478173DD68063');
        $this->addSql('ALTER TABLE usergroup_user DROP FOREIGN KEY FK_E9B5A1C0D2112630');
        $this->addSql('ALTER TABLE surveynotificationcollection_usergroup DROP FOREIGN KEY FK_A0F43A96D2112630');
        $this->addSql('ALTER TABLE surveynotificationcollection_usergroup DROP FOREIGN KEY FK_A0F43A96DC559D2C');
        $this->addSql('ALTER TABLE survey_notification DROP FOREIGN KEY FK_CF867A9ADF059805');
        $this->addSql('ALTER TABLE survey_link_click DROP FOREIGN KEY FK_503794C9EF1A9D84');
        $this->addSql('DROP TABLE user_group_collection');
        $this->addSql('DROP TABLE usergroupcollection_team');
        $this->addSql('DROP TABLE usergroupcollection_semester');
        $this->addSql('DROP TABLE usergroupcollection_user');
        $this->addSql('DROP TABLE usergroupcollection_department');
        $this->addSql('DROP TABLE usergroup');
        $this->addSql('DROP TABLE usergroup_user');
        $this->addSql('DROP TABLE survey_notification_collection');
        $this->addSql('DROP TABLE surveynotificationcollection_usergroup');
        $this->addSql('DROP TABLE survey_link_click');
        $this->addSql('DROP TABLE survey_notification');
        $this->addSql('ALTER TABLE survey ADD showCustomFinishPage TINYINT(1) NOT NULL DEFAULT \'0\'');
        $this->addSql('ALTER TABLE survey ADD teamSurvey TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('UPDATE survey SET teamSurvey=\'1\' WHERE targetAudience="1"');
        $this->addSql('ALTER TABLE survey DROP targetAudience');

    }
}
