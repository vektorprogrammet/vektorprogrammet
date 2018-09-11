<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180911180202 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admission_notification (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_EBEBA8557808B1AD (subscriber_id), INDEX IDX_EBEBA8554A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admission_subscriber (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, unsubscribeCode VARCHAR(255) NOT NULL, fromApplication TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_4F497EB7AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, user_id INT DEFAULT NULL, interview_id INT DEFAULT NULL, yearOfStudy VARCHAR(20) NOT NULL, monday TINYINT(1) DEFAULT \'1\' NOT NULL, tuesday TINYINT(1) DEFAULT \'1\' NOT NULL, wednesday TINYINT(1) DEFAULT \'1\' NOT NULL, thursday TINYINT(1) DEFAULT \'1\' NOT NULL, friday TINYINT(1) DEFAULT \'1\' NOT NULL, substitute TINYINT(1) DEFAULT \'0\' NOT NULL, language VARCHAR(255) DEFAULT NULL, doublePosition TINYINT(1) DEFAULT \'0\' NOT NULL, preferredGroup VARCHAR(255) DEFAULT NULL, preferredSchool VARCHAR(255) DEFAULT NULL, previousParticipation TINYINT(1) NOT NULL, last_edited DATETIME NOT NULL, created DATETIME NOT NULL, heardAboutFrom LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', teamInterest TINYINT(1) NOT NULL, specialNeeds VARCHAR(255) DEFAULT NULL, INDEX IDX_A45BDDC14A798B6F (semester_id), INDEX IDX_A45BDDC1A76ED395 (user_id), UNIQUE INDEX UNIQ_A45BDDC155D69D95 (interview_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_team (application_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_330CCE973E030ACD (application_id), INDEX IDX_330CCE97296CD8AE (team_id), PRIMARY KEY(application_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, article LONGTEXT NOT NULL, imageLarge VARCHAR(255) NOT NULL, imageSmall VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, sticky TINYINT(1) NOT NULL, published TINYINT(1) DEFAULT NULL, INDEX IDX_23A0E66F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_departments (article_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_B29B8FB57294869C (article_id), INDEX IDX_B29B8FB5AE80F5DF (department_id), PRIMARY KEY(article_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assistant_history (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, school_id INT DEFAULT NULL, workdays VARCHAR(255) NOT NULL, bolk VARCHAR(255) DEFAULT NULL, day VARCHAR(255) NOT NULL, INDEX IDX_1B31A1DBA76ED395 (user_id), INDEX IDX_1B31A1DB4A798B6F (semester_id), INDEX IDX_1B31A1DBC32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certificate_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_6E343C40A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) NOT NULL, short_name VARCHAR(50) NOT NULL, email VARCHAR(250) NOT NULL, address VARCHAR(250) DEFAULT NULL, city VARCHAR(250) NOT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, slackChannel VARCHAR(255) DEFAULT NULL, logo_path VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_CD1DE18A2D5B0234 (city), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department_school (department_id INT NOT NULL, school_id INT NOT NULL, INDEX IDX_ED84254BAE80F5DF (department_id), INDEX IDX_ED84254BC32A47EE (school_id), PRIMARY KEY(department_id, school_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE executive_board (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) NOT NULL, email VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, short_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE executive_board_membership (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, board_id INT DEFAULT NULL, positionName LONGTEXT DEFAULT NULL, startSemester_id INT DEFAULT NULL, endSemester_id INT DEFAULT NULL, INDEX IDX_F6490587A76ED395 (user_id), INDEX IDX_F6490587E7EC5785 (board_id), INDEX IDX_F6490587DD615B3E (startSemester_id), INDEX IDX_F6490587A1E6AC4E (endSemester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_of_study (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, name VARCHAR(250) NOT NULL, short_name VARCHAR(50) NOT NULL, INDEX IDX_8F32491AAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Image (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_4FC2B5B4E7AF8F (gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ImageGallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, referenceName VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_C689231577B2FEC3 (referenceName), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE infomeeting (id INT AUTO_INCREMENT NOT NULL, showOnPage TINYINT(1) DEFAULT NULL, date DATETIME DEFAULT NULL, room VARCHAR(250) DEFAULT NULL, description VARCHAR(250) DEFAULT NULL, link VARCHAR(250) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview (id INT AUTO_INCREMENT NOT NULL, schema_id INT DEFAULT NULL, interviewer_id INT DEFAULT NULL, interview_score_id INT DEFAULT NULL, user_id INT DEFAULT NULL, interviewed TINYINT(1) NOT NULL, scheduled DATETIME DEFAULT NULL, lastScheduleChanged DATETIME DEFAULT NULL, room VARCHAR(255) DEFAULT NULL, campus VARCHAR(255) DEFAULT NULL, mapLink VARCHAR(500) DEFAULT NULL, conducted DATETIME DEFAULT NULL, interviewStatus INT NOT NULL, responseCode VARCHAR(255) DEFAULT NULL, cancelMessage VARCHAR(255) DEFAULT NULL, newTimeMessage VARCHAR(2000) NOT NULL, coInterviewer_id INT DEFAULT NULL, INDEX IDX_CF1D3C34EA1BEF35 (schema_id), INDEX IDX_CF1D3C347906D9E8 (interviewer_id), INDEX IDX_CF1D3C348F5D2CA9 (coInterviewer_id), UNIQUE INDEX UNIQ_CF1D3C347B23C9B2 (interview_score_id), INDEX IDX_CF1D3C34A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_answer (id INT AUTO_INCREMENT NOT NULL, interview_id INT DEFAULT NULL, question_id INT DEFAULT NULL, answer LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_BA24465255D69D95 (interview_id), INDEX IDX_BA2446521E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_question (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, help VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_question_alternative (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, alternative VARCHAR(255) NOT NULL, INDEX IDX_711360B41E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_schema (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_schemas_questions (schema_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_F5387490EA1BEF35 (schema_id), INDEX IDX_F53874901E27F6BF (question_id), PRIMARY KEY(schema_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_score (id INT AUTO_INCREMENT NOT NULL, explanatoryPower INT NOT NULL, roleModel INT NOT NULL, suitability INT NOT NULL, suitableAssistant VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE letter (id INT AUTO_INCREMENT NOT NULL, newsletter_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, recipientCount INT NOT NULL, timestamp DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_8E02EE0A22DB1917 (newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, showOnAdmissionPage TINYINT(1) NOT NULL, INDEX IDX_7E8585C8AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, hashedResetCode VARCHAR(255) NOT NULL, resetTime DATETIME NOT NULL, INDEX IDX_B10172528D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, submitDate DATETIME DEFAULT NULL, receiptDate DATETIME NOT NULL, refundDate DATETIME DEFAULT NULL, picture_path VARCHAR(255) DEFAULT NULL, description VARCHAR(5000) NOT NULL, sum DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, visual_id VARCHAR(255) DEFAULT NULL, INDEX IDX_5399B645A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, role VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, contactPerson VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, international TINYINT(1) NOT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school_capacity (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, monday INT NOT NULL, tuesday INT NOT NULL, wednesday INT NOT NULL, thursday INT NOT NULL, friday INT NOT NULL, INDEX IDX_4BAE8530C32A47EE (school_id), INDEX IDX_4BAE85304A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semester (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, semesterTime VARCHAR(255) NOT NULL, year VARCHAR(255) NOT NULL, admission_start_date DATETIME NOT NULL, admission_end_date DATETIME NOT NULL, semesterStartDate DATETIME NOT NULL, semesterEndDate DATETIME NOT NULL, infoMeeting_id INT DEFAULT NULL, INDEX IDX_F7388EEDAE80F5DF (department_id), UNIQUE INDEX UNIQ_F7388EEDC8F2226C (infoMeeting_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE signature (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, signature_path VARCHAR(45) DEFAULT NULL, description VARCHAR(250) NOT NULL, UNIQUE INDEX UNIQ_AE880141A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, logoImagePath VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE static_content (id INT AUTO_INCREMENT NOT NULL, html_id VARCHAR(50) NOT NULL, html LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber (id INT AUTO_INCREMENT NOT NULL, newsletter_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, unsubscribeCode VARCHAR(255) NOT NULL, INDEX IDX_AD005B6922DB1917 (newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, showCustomFinishPage TINYINT(1) DEFAULT NULL, finishPageContent LONGTEXT DEFAULT NULL, confidential TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_AD5F9BFC4A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_surveys_questions (survey_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_DD4B0A34B3FE509D (survey_id), INDEX IDX_DD4B0A341E27F6BF (question_id), PRIMARY KEY(survey_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, survey_taken_id INT DEFAULT NULL, answer LONGTEXT DEFAULT NULL, answerArray LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_F2D382491E27F6BF (question_id), INDEX IDX_F2D382494D16DAC3 (survey_taken_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_question (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, optional TINYINT(1) NOT NULL, help VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_question_alternative (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, alternative VARCHAR(255) NOT NULL, INDEX IDX_D959462E1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_taken (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, time DATETIME NOT NULL, INDEX IDX_B3982430C32A47EE (school_id), INDEX IDX_B3982430B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, name VARCHAR(250) NOT NULL, email VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, short_description VARCHAR(255) DEFAULT NULL, acceptApplication TINYINT(1) DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX IDX_C4E0A61FAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_application (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, fieldOfStudy VARCHAR(255) NOT NULL, yearOfStudy VARCHAR(255) NOT NULL, motivationText LONGTEXT NOT NULL, biography LONGTEXT NOT NULL, phone VARCHAR(255) NOT NULL, INDEX IDX_4F6B328C296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE TeamInterest (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_64833834A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teaminterest_team (teaminterest_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_381349F87077D34 (teaminterest_id), INDEX IDX_381349F8296CD8AE (team_id), PRIMARY KEY(teaminterest_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_membership (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, team_id INT DEFAULT NULL, position_id INT DEFAULT NULL, deletedTeamName VARCHAR(255) DEFAULT NULL, isTeamLeader TINYINT(1) NOT NULL, startSemester_id INT DEFAULT NULL, endSemester_id INT DEFAULT NULL, INDEX IDX_B826A040A76ED395 (user_id), INDEX IDX_B826A040DD615B3E (startSemester_id), INDEX IDX_B826A040A1E6AC4E (endSemester_id), INDEX IDX_B826A040296CD8AE (team_id), INDEX IDX_B826A040DD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, lastName VARCHAR(255) NOT NULL, firstName VARCHAR(255) NOT NULL, gender TINYINT(1) NOT NULL, picture_path VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, accountNumber VARCHAR(45) DEFAULT NULL, user_name VARCHAR(255) DEFAULT NULL, password VARCHAR(64) DEFAULT NULL, email VARCHAR(255) NOT NULL, companyEmail VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, new_user_code VARCHAR(255) DEFAULT NULL, fieldOfStudy_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64924A232CF (user_name), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649381B3985 (companyEmail), INDEX IDX_8D93D64970395534 (fieldOfStudy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE elfinder_file (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, name VARCHAR(255) NOT NULL, content LONGBLOB NOT NULL, size INT NOT NULL, mtime INT NOT NULL, mime VARCHAR(255) NOT NULL, `read` VARCHAR(255) NOT NULL, `write` VARCHAR(255) NOT NULL, locked VARCHAR(255) NOT NULL, hidden VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, INDEX parent_id (parent_id), UNIQUE INDEX parent_name (parent_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA8557808B1AD FOREIGN KEY (subscriber_id) REFERENCES admission_subscriber (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA8554A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE admission_subscriber ADD CONSTRAINT FK_4F497EB7AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC14A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC155D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE application_team ADD CONSTRAINT FK_330CCE973E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_team ADD CONSTRAINT FK_330CCE97296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE articles_departments ADD CONSTRAINT FK_B29B8FB57294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE articles_departments ADD CONSTRAINT FK_B29B8FB5AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DB4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DBC32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE certificate_request ADD CONSTRAINT FK_6E343C40A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE department_school ADD CONSTRAINT FK_ED84254BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE department_school ADD CONSTRAINT FK_ED84254BC32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587E7EC5785 FOREIGN KEY (board_id) REFERENCES executive_board (id)');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587DD615B3E FOREIGN KEY (startSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A1E6AC4E FOREIGN KEY (endSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE field_of_study ADD CONSTRAINT FK_8F32491AAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE Image ADD CONSTRAINT FK_4FC2B5B4E7AF8F FOREIGN KEY (gallery_id) REFERENCES ImageGallery (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34EA1BEF35 FOREIGN KEY (schema_id) REFERENCES interview_schema (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C347906D9E8 FOREIGN KEY (interviewer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C348F5D2CA9 FOREIGN KEY (coInterviewer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C347B23C9B2 FOREIGN KEY (interview_score_id) REFERENCES interview_score (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview_answer ADD CONSTRAINT FK_BA24465255D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview_answer ADD CONSTRAINT FK_BA2446521E27F6BF FOREIGN KEY (question_id) REFERENCES interview_question (id)');
        $this->addSql('ALTER TABLE interview_question_alternative ADD CONSTRAINT FK_711360B41E27F6BF FOREIGN KEY (question_id) REFERENCES interview_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview_schemas_questions ADD CONSTRAINT FK_F5387490EA1BEF35 FOREIGN KEY (schema_id) REFERENCES interview_schema (id)');
        $this->addSql('ALTER TABLE interview_schemas_questions ADD CONSTRAINT FK_F53874901E27F6BF FOREIGN KEY (question_id) REFERENCES interview_question (id)');
        $this->addSql('ALTER TABLE letter ADD CONSTRAINT FK_8E02EE0A22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B10172528D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE school_capacity ADD CONSTRAINT FK_4BAE8530C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE school_capacity ADD CONSTRAINT FK_4BAE85304A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDC8F2226C FOREIGN KEY (infoMeeting_id) REFERENCES infomeeting (id)');
        $this->addSql('ALTER TABLE signature ADD CONSTRAINT FK_AE880141A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B6922DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE survey_surveys_questions ADD CONSTRAINT FK_DD4B0A34B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_surveys_questions ADD CONSTRAINT FK_DD4B0A341E27F6BF FOREIGN KEY (question_id) REFERENCES survey_question (id)');
        $this->addSql('ALTER TABLE survey_answer ADD CONSTRAINT FK_F2D382491E27F6BF FOREIGN KEY (question_id) REFERENCES survey_question (id)');
        $this->addSql('ALTER TABLE survey_answer ADD CONSTRAINT FK_F2D382494D16DAC3 FOREIGN KEY (survey_taken_id) REFERENCES survey_taken (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_question_alternative ADD CONSTRAINT FK_D959462E1E27F6BF FOREIGN KEY (question_id) REFERENCES survey_question (id)');
        $this->addSql('ALTER TABLE survey_taken ADD CONSTRAINT FK_B3982430C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE survey_taken ADD CONSTRAINT FK_B3982430B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE team_application ADD CONSTRAINT FK_4F6B328C296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE TeamInterest ADD CONSTRAINT FK_64833834A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE teaminterest_team ADD CONSTRAINT FK_381349F87077D34 FOREIGN KEY (teaminterest_id) REFERENCES TeamInterest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teaminterest_team ADD CONSTRAINT FK_381349F8296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040DD615B3E FOREIGN KEY (startSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040A1E6AC4E FOREIGN KEY (endSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64970395534 FOREIGN KEY (fieldOfStudy_id) REFERENCES field_of_study (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admission_notification DROP FOREIGN KEY FK_EBEBA8557808B1AD');
        $this->addSql('ALTER TABLE application_team DROP FOREIGN KEY FK_330CCE973E030ACD');
        $this->addSql('ALTER TABLE articles_departments DROP FOREIGN KEY FK_B29B8FB57294869C');
        $this->addSql('ALTER TABLE admission_subscriber DROP FOREIGN KEY FK_4F497EB7AE80F5DF');
        $this->addSql('ALTER TABLE articles_departments DROP FOREIGN KEY FK_B29B8FB5AE80F5DF');
        $this->addSql('ALTER TABLE department_school DROP FOREIGN KEY FK_ED84254BAE80F5DF');
        $this->addSql('ALTER TABLE field_of_study DROP FOREIGN KEY FK_8F32491AAE80F5DF');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8AE80F5DF');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDAE80F5DF');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FAE80F5DF');
        $this->addSql('ALTER TABLE executive_board_membership DROP FOREIGN KEY FK_F6490587E7EC5785');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64970395534');
        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5B4E7AF8F');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDC8F2226C');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC155D69D95');
        $this->addSql('ALTER TABLE interview_answer DROP FOREIGN KEY FK_BA24465255D69D95');
        $this->addSql('ALTER TABLE interview_answer DROP FOREIGN KEY FK_BA2446521E27F6BF');
        $this->addSql('ALTER TABLE interview_question_alternative DROP FOREIGN KEY FK_711360B41E27F6BF');
        $this->addSql('ALTER TABLE interview_schemas_questions DROP FOREIGN KEY FK_F53874901E27F6BF');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34EA1BEF35');
        $this->addSql('ALTER TABLE interview_schemas_questions DROP FOREIGN KEY FK_F5387490EA1BEF35');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C347B23C9B2');
        $this->addSql('ALTER TABLE letter DROP FOREIGN KEY FK_8E02EE0A22DB1917');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B6922DB1917');
        $this->addSql('ALTER TABLE team_membership DROP FOREIGN KEY FK_B826A040DD842E46');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE assistant_history DROP FOREIGN KEY FK_1B31A1DBC32A47EE');
        $this->addSql('ALTER TABLE department_school DROP FOREIGN KEY FK_ED84254BC32A47EE');
        $this->addSql('ALTER TABLE school_capacity DROP FOREIGN KEY FK_4BAE8530C32A47EE');
        $this->addSql('ALTER TABLE survey_taken DROP FOREIGN KEY FK_B3982430C32A47EE');
        $this->addSql('ALTER TABLE admission_notification DROP FOREIGN KEY FK_EBEBA8554A798B6F');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC14A798B6F');
        $this->addSql('ALTER TABLE assistant_history DROP FOREIGN KEY FK_1B31A1DB4A798B6F');
        $this->addSql('ALTER TABLE executive_board_membership DROP FOREIGN KEY FK_F6490587DD615B3E');
        $this->addSql('ALTER TABLE executive_board_membership DROP FOREIGN KEY FK_F6490587A1E6AC4E');
        $this->addSql('ALTER TABLE school_capacity DROP FOREIGN KEY FK_4BAE85304A798B6F');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC4A798B6F');
        $this->addSql('ALTER TABLE TeamInterest DROP FOREIGN KEY FK_64833834A798B6F');
        $this->addSql('ALTER TABLE team_membership DROP FOREIGN KEY FK_B826A040DD615B3E');
        $this->addSql('ALTER TABLE team_membership DROP FOREIGN KEY FK_B826A040A1E6AC4E');
        $this->addSql('ALTER TABLE survey_surveys_questions DROP FOREIGN KEY FK_DD4B0A34B3FE509D');
        $this->addSql('ALTER TABLE survey_taken DROP FOREIGN KEY FK_B3982430B3FE509D');
        $this->addSql('ALTER TABLE survey_surveys_questions DROP FOREIGN KEY FK_DD4B0A341E27F6BF');
        $this->addSql('ALTER TABLE survey_answer DROP FOREIGN KEY FK_F2D382491E27F6BF');
        $this->addSql('ALTER TABLE survey_question_alternative DROP FOREIGN KEY FK_D959462E1E27F6BF');
        $this->addSql('ALTER TABLE survey_answer DROP FOREIGN KEY FK_F2D382494D16DAC3');
        $this->addSql('ALTER TABLE application_team DROP FOREIGN KEY FK_330CCE97296CD8AE');
        $this->addSql('ALTER TABLE team_application DROP FOREIGN KEY FK_4F6B328C296CD8AE');
        $this->addSql('ALTER TABLE teaminterest_team DROP FOREIGN KEY FK_381349F8296CD8AE');
        $this->addSql('ALTER TABLE team_membership DROP FOREIGN KEY FK_B826A040296CD8AE');
        $this->addSql('ALTER TABLE teaminterest_team DROP FOREIGN KEY FK_381349F87077D34');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE assistant_history DROP FOREIGN KEY FK_1B31A1DBA76ED395');
        $this->addSql('ALTER TABLE certificate_request DROP FOREIGN KEY FK_6E343C40A76ED395');
        $this->addSql('ALTER TABLE executive_board_membership DROP FOREIGN KEY FK_F6490587A76ED395');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C347906D9E8');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C348F5D2CA9');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34A76ED395');
        $this->addSql('ALTER TABLE password_reset DROP FOREIGN KEY FK_B10172528D93D649');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645A76ED395');
        $this->addSql('ALTER TABLE signature DROP FOREIGN KEY FK_AE880141A76ED395');
        $this->addSql('ALTER TABLE team_membership DROP FOREIGN KEY FK_B826A040A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('DROP TABLE admission_notification');
        $this->addSql('DROP TABLE admission_subscriber');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE application_team');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE articles_departments');
        $this->addSql('DROP TABLE assistant_history');
        $this->addSql('DROP TABLE certificate_request');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE department_school');
        $this->addSql('DROP TABLE executive_board');
        $this->addSql('DROP TABLE executive_board_membership');
        $this->addSql('DROP TABLE field_of_study');
        $this->addSql('DROP TABLE Image');
        $this->addSql('DROP TABLE ImageGallery');
        $this->addSql('DROP TABLE infomeeting');
        $this->addSql('DROP TABLE interview');
        $this->addSql('DROP TABLE interview_answer');
        $this->addSql('DROP TABLE interview_question');
        $this->addSql('DROP TABLE interview_question_alternative');
        $this->addSql('DROP TABLE interview_schema');
        $this->addSql('DROP TABLE interview_schemas_questions');
        $this->addSql('DROP TABLE interview_score');
        $this->addSql('DROP TABLE letter');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE password_reset');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE school_capacity');
        $this->addSql('DROP TABLE semester');
        $this->addSql('DROP TABLE signature');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE static_content');
        $this->addSql('DROP TABLE subscriber');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE survey_surveys_questions');
        $this->addSql('DROP TABLE survey_answer');
        $this->addSql('DROP TABLE survey_question');
        $this->addSql('DROP TABLE survey_question_alternative');
        $this->addSql('DROP TABLE survey_taken');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_application');
        $this->addSql('DROP TABLE TeamInterest');
        $this->addSql('DROP TABLE teaminterest_team');
        $this->addSql('DROP TABLE team_membership');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE elfinder_file');
    }
}
