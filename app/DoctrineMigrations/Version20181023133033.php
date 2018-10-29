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

        /** Set all semesters to the semester with department_id = 1 */
        $this->addSql('
            UPDATE assistant_history
            INNER JOIN semester AS semester_old ON semester_id = semester_old.id
            INNER JOIN semester AS semester_new ON (
                semester_old.semesterTime = semester_new.semesterTime AND
                semester_old.year = semester_new.year AND 
                semester_new.department_id = 1
            )
            SET semester_id = semester_new.id
        ');

        $this->addSql('
            UPDATE school_capacity
            INNER JOIN semester AS semester_old ON semester_id = semester_old.id
            INNER JOIN semester AS semester_new ON (
                semester_old.semesterTime = semester_new.semesterTime AND
                semester_old.year = semester_new.year AND
                semester_new.department_id = 1
            )
            SET semester_id = semester_new.id
        ');

        $this->addSql('
            UPDATE admission_notification
            INNER JOIN semester AS semester_old ON semester_id = semester_old.id
            INNER JOIN semester AS semester_new ON (
                semester_old.semesterTime = semester_new.semesterTime AND
                semester_old.year = semester_new.year AND
                semester_new.department_id = 1
            )
            SET semester_id = semester_new.id
        ');

        $this->addSql('
            UPDATE survey
            INNER JOIN semester AS semester_old ON semester_id = semester_old.id
            INNER JOIN semester AS semester_new ON (
                semester_old.semesterTime = semester_new.semesterTime AND
                semester_old.year = semester_new.year AND
                semester_new.department_id = 1
            )
            SET semester_id = semester_new.id
        ');

        $this->addSql('
            UPDATE TeamInterest
            INNER JOIN semester AS semester_old ON semester_id = semester_old.id
            INNER JOIN semester AS semester_new ON (
                semester_old.semesterTime = semester_new.semesterTime AND
                semester_old.year = semester_new.year AND
                semester_new.department_id = 1
            )
            SET semester_id = semester_new.id
        ');

        $this->addSql('
            UPDATE AdmissionPeriod
            INNER JOIN semester AS semester_old ON semester_id = semester_old.id
            INNER JOIN semester AS semester_new ON (
                semester_old.semesterTime = semester_new.semesterTime AND
                semester_old.year = semester_new.year AND
                semester_new.department_id = 1
            )
            SET semester_id = semester_new.id
        ');

        $this->addSql('
            UPDATE team_membership
            INNER JOIN semester AS startSemester_old ON startSemester_id = startSemester_old.id
            INNER JOIN semester AS startSemester_new ON (
                startSemester_old.semesterTime = startSemester_new.semesterTime AND
                startSemester_old.year = startSemester_new.year AND
                startSemester_new.department_id = 1
            )
            LEFT JOIN semester AS endSemester_old ON endSemester_id = endSemester_old.id
            LEFT JOIN semester AS endSemester_new ON (
                endSemester_old.semesterTime = endSemester_new.semesterTime AND
                endSemester_old.year = endSemester_new.year AND
                endSemester_new.department_id = 1
            )
            SET startSemester_id = startSemester_new.id,
                endSemester_id = endSemester_new.id
        ');


        $this->addSql('
            UPDATE executive_board_membership
            INNER JOIN semester AS startSemester_old ON startSemester_id = startSemester_old.id
            INNER JOIN semester AS startSemester_new ON (
                startSemester_old.semesterTime = startSemester_new.semesterTime AND
                startSemester_old.year = startSemester_new.year AND
                startSemester_new.department_id = 1
            )
            LEFT JOIN semester AS endSemester_old ON endSemester_id = endSemester_old.id
            LEFT JOIN semester AS endSemester_new ON (
                endSemester_old.semesterTime = endSemester_new.semesterTime AND
                endSemester_old.year = endSemester_new.year AND
                endSemester_new.department_id = 1
            )
            SET startSemester_id = startSemester_new.id,
                endSemester_id = endSemester_new.id
        ');
        /** Delete FK and IDX for semester_id */
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC14A798B6F');
        $this->addSql('DROP INDEX IDX_A45BDDC14A798B6F ON application');

        /** Drop semester_id from Application */
        $this->addSql('ALTER TABLE application DROP COLUMN semester_id');

        /** Drop unused tables */
        $this->addSql('DROP TABLE interview_practical');
        $this->addSql('DROP TABLE application_statistic');
        $this->addSql('DROP TABLE substitute');

        /** Delete all other semesters */
        $this->addSql('DELETE FROM semester WHERE department_id != 1');

        /** Drop old columns in semester */
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_E4EECBBAE80F5DF');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDC8F2226C');
        $this->addSql('DROP INDEX UNIQ_F7388EEDC8F2226C ON semester');
        $this->addSql('DROP INDEX IDX_E4EECBBAE80F5DF ON semester');
        $this->addSql('ALTER TABLE semester DROP COLUMN department_id');
        $this->addSql('ALTER TABLE semester DROP COLUMN admission_start_date');
        $this->addSql('ALTER TABLE semester DROP COLUMN admission_end_date');
        $this->addSql('ALTER TABLE semester DROP COLUMN semesterStartDate');
        $this->addSql('ALTER TABLE semester DROP COLUMN semesterEndDate');
        $this->addSql('ALTER TABLE semester DROP COLUMN infoMeeting_id');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Yeah, imma stop you right there');
    }
}
