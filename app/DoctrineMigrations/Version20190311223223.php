<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190311223223 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE usergroup_collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, numberUserGroups INT NOT NULL, numberTotalUsers INT DEFAULT NULL, assistantBolks LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', isActive TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_team (usergroupcollection_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_9CDC588ED3EE73A6 (usergroupcollection_id), INDEX IDX_9CDC588E296CD8AE (team_id), PRIMARY KEY(usergroupcollection_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_semester (usergroupcollection_id INT NOT NULL, semester_id INT NOT NULL, INDEX IDX_5BDDF6CFD3EE73A6 (usergroupcollection_id), INDEX IDX_5BDDF6CF4A798B6F (semester_id), PRIMARY KEY(usergroupcollection_id, semester_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroupcollection_department (usergroupcollection_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_3471F241D3EE73A6 (usergroupcollection_id), INDEX IDX_3471F241AE80F5DF (department_id), PRIMARY KEY(usergroupcollection_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroup (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, isActive TINYINT(1) NOT NULL, userGroupCollection_id INT DEFAULT NULL, INDEX IDX_4A6478173DD68063 (userGroupCollection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usergroup_user (usergroup_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E9B5A1C0D2112630 (usergroup_id), INDEX IDX_E9B5A1C0A76ED395 (user_id), PRIMARY KEY(usergroup_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_notifier (id INT AUTO_INCREMENT NOT NULL, survey_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, timeOfNotification DATETIME NOT NULL, notificationType INT NOT NULL, isAllSent TINYINT(1) NOT NULL, isActive TINYINT(1) DEFAULT \'0\' NOT NULL, userGroup_id INT DEFAULT NULL, INDEX IDX_7A119AA22B674466 (userGroup_id), INDEX IDX_7A119AA2B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, timeOfFirstVisit DATETIME DEFAULT NULL, timeNotificationSent DATETIME DEFAULT NULL, userIdentifier VARCHAR(255) NOT NULL, isSent TINYINT(1) NOT NULL, surveyNotifier_id INT DEFAULT NULL, INDEX IDX_CF867A9AA76ED395 (user_id), INDEX IDX_CF867A9A45F6A4BB (surveyNotifier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usergroupcollection_team ADD CONSTRAINT FK_9CDC588ED3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES usergroup_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_team ADD CONSTRAINT FK_9CDC588E296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_semester ADD CONSTRAINT FK_5BDDF6CFD3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES usergroup_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_semester ADD CONSTRAINT FK_5BDDF6CF4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_department ADD CONSTRAINT FK_3471F241D3EE73A6 FOREIGN KEY (usergroupcollection_id) REFERENCES usergroup_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroupcollection_department ADD CONSTRAINT FK_3471F241AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroup ADD CONSTRAINT FK_4A6478173DD68063 FOREIGN KEY (userGroupCollection_id) REFERENCES usergroup_collection (id)');
        $this->addSql('ALTER TABLE usergroup_user ADD CONSTRAINT FK_E9B5A1C0D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usergroup_user ADD CONSTRAINT FK_E9B5A1C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_notifier ADD CONSTRAINT FK_7A119AA22B674466 FOREIGN KEY (userGroup_id) REFERENCES usergroup (id)');
        $this->addSql('ALTER TABLE survey_notifier ADD CONSTRAINT FK_7A119AA2B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_notification ADD CONSTRAINT FK_CF867A9AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE survey_notification ADD CONSTRAINT FK_CF867A9A45F6A4BB FOREIGN KEY (surveyNotifier_id) REFERENCES survey_notifier (id)');
        $this->addSql('ALTER TABLE survey ADD targetAudience INT DEFAULT 0 NOT NULL, DROP teamSurvey');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE usergroupcollection_team DROP FOREIGN KEY FK_9CDC588ED3EE73A6');
        $this->addSql('ALTER TABLE usergroupcollection_semester DROP FOREIGN KEY FK_5BDDF6CFD3EE73A6');
        $this->addSql('ALTER TABLE usergroupcollection_department DROP FOREIGN KEY FK_3471F241D3EE73A6');
        $this->addSql('ALTER TABLE usergroup DROP FOREIGN KEY FK_4A6478173DD68063');
        $this->addSql('ALTER TABLE usergroup_user DROP FOREIGN KEY FK_E9B5A1C0D2112630');
        $this->addSql('ALTER TABLE survey_notifier DROP FOREIGN KEY FK_7A119AA22B674466');
        $this->addSql('ALTER TABLE survey_notification DROP FOREIGN KEY FK_CF867A9A45F6A4BB');
        $this->addSql('DROP TABLE usergroup_collection');
        $this->addSql('DROP TABLE usergroupcollection_team');
        $this->addSql('DROP TABLE usergroupcollection_semester');
        $this->addSql('DROP TABLE usergroupcollection_department');
        $this->addSql('DROP TABLE usergroup');
        $this->addSql('DROP TABLE usergroup_user');
        $this->addSql('DROP TABLE survey_notifier');
        $this->addSql('DROP TABLE survey_notification');
        $this->addSql('ALTER TABLE survey ADD teamSurvey TINYINT(1) DEFAULT \'0\' NOT NULL, DROP targetAudience');
    }
}
