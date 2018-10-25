<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20181023133033 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /** Create AdmissionPeriod */
        $this->addSql('CREATE TABLE AdmissionPeriod (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, admission_start_date DATETIME NOT NULL, admission_end_date DATETIME NOT NULL, infoMeeting_id INT DEFAULT NULL, INDEX IDX_E66C4D9BAE80F5DF (department_id), UNIQUE INDEX UNIQ_E66C4D9BC8F2226C (infoMeeting_id), INDEX IDX_E66C4D9B4A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE AdmissionPeriod ADD CONSTRAINT FK_E66C4D9BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE AdmissionPeriod ADD CONSTRAINT FK_E66C4D9BC8F2226C FOREIGN KEY (infoMeeting_id) REFERENCES infomeeting (id)');
        $this->addSql('ALTER TABLE AdmissionPeriod ADD CONSTRAINT FK_E66C4D9B4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');

        /** Create AdmissionPeriods from old Semesters. Note, old semester_ids inserted */
        $this->addSql('
            INSERT INTO AdmissionPeriod (department_id, semester_id, admission_start_date, admission_end_date, infoMeeting_id)
            SELECT department_id, id, admission_start_date, admission_end_date, infoMeeting_id FROM semester
        ');


        /** Add and set Department for AssistantHistory */
        $this->addSql('ALTER TABLE assistant_history ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DBAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1B31A1DBAE80F5DF ON assistant_history (department_id)');
        $this->addSql('
            UPDATE assistant_history
            INNER JOIN semester ON semester.id = semester_id
            SET assistant_history.department_id = semester.department_id
        ');

        /** Add and set Department for SchoolCapacity */
        $this->addSql('ALTER TABLE school_capacity ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE school_capacity ADD CONSTRAINT FK_4BAE8530AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_4BAE8530AE80F5DF ON school_capacity (department_id)');
        $this->addSql('
            UPDATE school_capacity
            INNER JOIN semester ON semester.id = semester_id
            SET school_capacity.department_id = semester.department_id
        ');

        /** Add and set Department for AdmissionNotification */
        $this->addSql('ALTER TABLE admission_notification ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA855AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_EBEBA855AE80F5DF ON admission_notification (department_id)');
        $this->addSql('
            UPDATE admission_notification
            INNER JOIN semester ON semester.id = semester_id
            SET admission_notification.department_id = semester.department_id
        ');

        /** Add and set Department for Survey */
        $this->addSql('ALTER TABLE survey ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFCAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_AD5F9BFCAE80F5DF ON survey (department_id)');
        $this->addSql('
            UPDATE survey
            INNER JOIN semester ON semester.id = semester_id
            SET survey.department_id = semester.department_id
        ');

        /** Add and set Department for TeamInterest */
        $this->addSql('ALTER TABLE TeamInterest ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE TeamInterest ADD CONSTRAINT FK_6483383AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_6483383AE80F5DF ON TeamInterest (department_id)');
        $this->addSql('
            UPDATE TeamInterest
            INNER JOIN semester ON semester.id = semester_id
            SET TeamInterest.department_id = semester.department_id
        ');

        /** Add and set AdmissionPeriod in Application */
        $this->addSql('ALTER TABLE application ADD admissionPeriod_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1CB9236CC FOREIGN KEY (admissionPeriod_id) REFERENCES AdmissionPeriod (id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1CB9236CC ON application (admissionPeriod_id)');
        $this->addSql('
            UPDATE application
            INNER JOIN semester ON semester.id = application.semester_id
            INNER JOIN AdmissionPeriod ON AdmissionPeriod.semester_id = semester.id
            SET AdmissionPeriod_id = AdmissionPeriod.id
        ');

        /** At this point, all tables should have old semester_ids, we need to create new semesters and replace the semester ids */

        /** Create new semester table, which we will later replace the old semester table with */
        $this->addSql('
            CREATE TABLE semester_new (
                id INT AUTO_INCREMENT NOT NULL,
                semesterTime VARCHAR(255) NOT NULL,
                year VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');

        /** Create new semesters */
        $this->addSql('
            INSERT INTO semester_new (semesterTime, year)
            SELECT DISTINCT semesterTime, year FROM semester
        ');

        /** Insert new semester_ids in AssistantHistory */
        /* TODO: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint
        fails (`vektorprogrammet`.`assistant_history`, CONSTRAINT `FK_8E3E940D4A798B6F` FOREIGN KEY (`semester_id`)
        REFERENCES `semester` (`id`) ON DELETE SET NULL) */
        $this->addSql('
            UPDATE assistant_history
            INNER JOIN semester ON semester_id = semester.id
            INNER JOIN semester_new ON (semester.semesterTime = semester_new.semesterTime AND semester.year = semester_new.year)
            SET semester_id = semester_new.id
        ');

        /** Insert new semester_ids in SchoolCapacity */
        $this->addSql('
            UPDATE school_capacity
            INNER JOIN semester ON semester_id = semester.id
            INNER JOIN semester_new ON (semester.semesterTime = semester_new.semesterTime AND semester.year = semester_new.year)
            SET semester_id = semester_new.id
        ');

        /** Insert new semester_ids in AdmissionNotification */
        $this->addSql('
            UPDATE admission_notification
            INNER JOIN semester on semester_id = semester.id
            INNER JOIN semester_new ON (semester.semesterTime = semester_new.semesterTime AND semester.year = semester_new.year)
            SET semester_id = semester_new.id
        ');

        /** Insert new semester_ids in Survey */
        $this->addSql('
            UPDATE survey
            INNER JOIN semester on semester_id = semester.id
            INNER JOIN semester_new ON (semester.semesterTime = semester_new.semesterTime AND semester.year = semester_new.year)
            SET semester_id = semester_new.id
        ');

        /** Insert new semester_ids in TeamInterest */
        $this->addSql('
            UPDATE TeamInterest
            INNER JOIN semester on semester_id = semester.id
            INNER JOIN semester_new ON (semester.semesterTime = semester_new.semesterTime AND semester.year = semester_new.year)
            SET semester_id = semester_new.id
        ');

        /** Insert new semester_ids in AdmissionPeriod */
        $this->addSql('
            UPDATE AdmissionPeriod
            INNER JOIN semester on semester_id = semester.id
            INNER JOIN semester_new ON (semester.semesterTime = semester_new.semesterTime AND semester.year = semester_new.year)
            SET semester_id = semester_new.id
        ');

        /** Drop old Semester */
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDAE80F5DF');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDC8F2226C');
        $this->addSql('DROP INDEX UNIQ_F7388EEDC8F2226C ON semester');
        $this->addSql('DROP INDEX IDX_F7388EEDAE80F5DF ON semester');
        $this->addSql('DROP TABLE semester'); // Rip

        /** These were autogenerated, so we probably need them */
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC14A798B6F');
        $this->addSql('DROP INDEX IDX_A45BDDC14A798B6F ON application');

        /** Drop semester_id from Application */
        $this->addSql('ALTER TABLE application DROP COLUMN semester_id');

        /** Finally rename it */
        $this->addSql('ALTER TABLE semester_new RENAME TO semester');
    }

    public function down(Schema $schema) : void
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
        $this->addSql('ALTER TABLE school_capacity DROP FOREIGN KEY FK_4BAE8530AE80F5DF');
        $this->addSql('DROP INDEX IDX_4BAE8530AE80F5DF ON school_capacity');
        $this->addSql('ALTER TABLE school_capacity DROP department_id');
        $this->addSql('ALTER TABLE semester ADD department_id INT DEFAULT NULL, ADD admission_start_date DATETIME NOT NULL, ADD admission_end_date DATETIME NOT NULL, ADD semesterStartDate DATETIME NOT NULL, ADD semesterEndDate DATETIME NOT NULL, ADD infoMeeting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDC8F2226C FOREIGN KEY (infoMeeting_id) REFERENCES infomeeting (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7388EEDC8F2226C ON semester (infoMeeting_id)');
        $this->addSql('CREATE INDEX IDX_F7388EEDAE80F5DF ON semester (department_id)');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFCAE80F5DF');
        $this->addSql('DROP INDEX IDX_AD5F9BFCAE80F5DF ON survey');
        $this->addSql('ALTER TABLE survey DROP department_id');
    }
}
