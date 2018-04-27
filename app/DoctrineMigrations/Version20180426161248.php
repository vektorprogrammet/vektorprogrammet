<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180426161248 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE executive_board_member TO executive_board_membership, work_history TO team_membership');

        $this->addSql('ALTER TABLE executive_board_membership CHANGE `position` `positionName` LONGTEXT');
        $this->addSql('ALTER TABLE executive_board_membership ADD startSemester_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE executive_board_membership ADD endSemester_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587DD615B3E FOREIGN KEY (startSemester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A1E6AC4E FOREIGN KEY (endSemester_id) REFERENCES semester (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE executive_board_membership TO executive_board_member, team_membership TO work_history');

        $this->addSql('ALTER TABLE executive_board_member CHANGE `positionName` `position` LONGTEXT');
        $this->addSql('ALTER TABLE executive_board_membership DROP startSemester_id');
        $this->addSql('ALTER TABLE executive_board_membership DROP endSemester_id');
    }
}
