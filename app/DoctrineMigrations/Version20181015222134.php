<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181015222134 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE AdmissionPeriod (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, admission_start_date DATETIME NOT NULL, admission_end_date DATETIME NOT NULL, infoMeeting_id INT DEFAULT NULL, INDEX IDX_E66C4D9BAE80F5DF (department_id), UNIQUE INDEX UNIQ_E66C4D9BC8F2226C (infoMeeting_id), INDEX IDX_E66C4D9B4A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE AdmissionPeriod ADD CONSTRAINT FK_E66C4D9BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE AdmissionPeriod ADD CONSTRAINT FK_E66C4D9BC8F2226C FOREIGN KEY (infoMeeting_id) REFERENCES infomeeting (id)');
        $this->addSql('ALTER TABLE AdmissionPeriod ADD CONSTRAINT FK_E66C4D9B4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE assistant_history ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DBAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1B31A1DBAE80F5DF ON assistant_history (department_id)');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC14A798B6F');
        $this->addSql('DROP INDEX IDX_A45BDDC14A798B6F ON application');
        $this->addSql('ALTER TABLE application CHANGE semester_id admissionPeriod_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1CB9236CC FOREIGN KEY (admissionPeriod_id) REFERENCES AdmissionPeriod (id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1CB9236CC ON application (admissionPeriod_id)');
        $this->addSql('ALTER TABLE admission_notification ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA855AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_EBEBA855AE80F5DF ON admission_notification (department_id)');
        $this->addSql('ALTER TABLE survey ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFCAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_AD5F9BFCAE80F5DF ON survey (department_id)');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDAE80F5DF');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDC8F2226C');
        $this->addSql('DROP INDEX UNIQ_F7388EEDC8F2226C ON semester');
        $this->addSql('DROP INDEX IDX_F7388EEDAE80F5DF ON semester');
        $this->addSql('ALTER TABLE semester DROP department_id, DROP admission_start_date, DROP admission_end_date, DROP infoMeeting_id');
        $this->addSql('ALTER TABLE TeamInterest ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE TeamInterest ADD CONSTRAINT FK_6483383AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_6483383AE80F5DF ON TeamInterest (department_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1CB9236CC');
        $this->addSql('DROP TABLE AdmissionPeriod');
        $this->addSql('ALTER TABLE TeamInterest DROP FOREIGN KEY FK_6483383AE80F5DF');
        $this->addSql('DROP INDEX IDX_6483383AE80F5DF ON TeamInterest');
        $this->addSql('ALTER TABLE TeamInterest DROP department_id');
        $this->addSql('ALTER TABLE admission_notification DROP FOREIGN KEY FK_EBEBA855AE80F5DF');
        $this->addSql('DROP INDEX IDX_EBEBA855AE80F5DF ON admission_notification');
        $this->addSql('ALTER TABLE admission_notification DROP department_id');
        $this->addSql('DROP INDEX IDX_A45BDDC1CB9236CC ON application');
        $this->addSql('ALTER TABLE application CHANGE admissionperiod_id semester_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC14A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC14A798B6F ON application (semester_id)');
        $this->addSql('ALTER TABLE assistant_history DROP FOREIGN KEY FK_1B31A1DBAE80F5DF');
        $this->addSql('DROP INDEX IDX_1B31A1DBAE80F5DF ON assistant_history');
        $this->addSql('ALTER TABLE assistant_history DROP department_id');
        $this->addSql('ALTER TABLE semester ADD department_id INT DEFAULT NULL, ADD admission_start_date DATETIME NOT NULL, ADD admission_end_date DATETIME NOT NULL, ADD infoMeeting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDC8F2226C FOREIGN KEY (infoMeeting_id) REFERENCES infomeeting (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7388EEDC8F2226C ON semester (infoMeeting_id)');
        $this->addSql('CREATE INDEX IDX_F7388EEDAE80F5DF ON semester (department_id)');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFCAE80F5DF');
        $this->addSql('DROP INDEX IDX_AD5F9BFCAE80F5DF ON survey');
        $this->addSql('ALTER TABLE survey DROP department_id');
    }
}
