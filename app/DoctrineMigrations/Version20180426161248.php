<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180426161248 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE executive_board_membership (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, board_id INT DEFAULT NULL, positionName LONGTEXT DEFAULT NULL, startSemester_id INT DEFAULT NULL, endSemester_id INT DEFAULT NULL, INDEX IDX_F6490587A76ED395 (user_id), INDEX IDX_F6490587E7EC5785 (board_id), INDEX IDX_F6490587DD615B3E (startSemester_id), INDEX IDX_F6490587A1E6AC4E (endSemester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_membership (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, team_id INT DEFAULT NULL, position_id INT DEFAULT NULL, deletedTeamName VARCHAR(255) DEFAULT NULL, isTeamLeader TINYINT(1) NOT NULL, startSemester_id INT DEFAULT NULL, endSemester_id INT DEFAULT NULL, INDEX IDX_B826A040A76ED395 (user_id), INDEX IDX_B826A040DD615B3E (startSemester_id), INDEX IDX_B826A040A1E6AC4E (endSemester_id), INDEX IDX_B826A040296CD8AE (team_id), INDEX IDX_B826A040DD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587E7EC5785 FOREIGN KEY (board_id) REFERENCES executive_board (id)');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587DD615B3E FOREIGN KEY (startSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A1E6AC4E FOREIGN KEY (endSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040DD615B3E FOREIGN KEY (startSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040A1E6AC4E FOREIGN KEY (endSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team_membership ADD CONSTRAINT FK_B826A040DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE executive_board_member');
        $this->addSql('DROP TABLE work_history');
        $this->addSql('ALTER TABLE receipt CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C348F5D2CA9');
        $this->addSql('DROP INDEX IDX_CF1D3C348F5D2CA9 ON interview');
        $this->addSql('ALTER TABLE interview DROP coInterviewer_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE executive_board_member (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, board_id INT DEFAULT NULL, position LONGTEXT DEFAULT NULL, INDEX IDX_1B0352A4A76ED395 (user_id), INDEX IDX_1B0352A4E7EC5785 (board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_history (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, user_id INT DEFAULT NULL, position_id INT DEFAULT NULL, deletedTeamName VARCHAR(255) DEFAULT NULL, isTeamLeader TINYINT(1) NOT NULL, startSemester_id INT DEFAULT NULL, endSemester_id INT DEFAULT NULL, INDEX IDX_F271C869A76ED395 (user_id), INDEX IDX_F271C869DD615B3E (startSemester_id), INDEX IDX_F271C869A1E6AC4E (endSemester_id), INDEX IDX_F271C869296CD8AE (team_id), INDEX IDX_F271C869DD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE executive_board_member ADD CONSTRAINT FK_1B0352A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE executive_board_member ADD CONSTRAINT FK_1B0352A4E7EC5785 FOREIGN KEY (board_id) REFERENCES executive_board (id)');
        $this->addSql('ALTER TABLE work_history ADD CONSTRAINT FK_F271C869296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE work_history ADD CONSTRAINT FK_F271C869A1E6AC4E FOREIGN KEY (endSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE work_history ADD CONSTRAINT FK_F271C869A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE work_history ADD CONSTRAINT FK_F271C869DD615B3E FOREIGN KEY (startSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE work_history ADD CONSTRAINT FK_F271C869DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE executive_board_membership');
        $this->addSql('DROP TABLE team_membership');
        $this->addSql('ALTER TABLE interview ADD coInterviewer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C348F5D2CA9 FOREIGN KEY (coInterviewer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C348F5D2CA9 ON interview (coInterviewer_id)');
        $this->addSql('ALTER TABLE receipt CHANGE description description VARCHAR(5000) NOT NULL');
    }
}
