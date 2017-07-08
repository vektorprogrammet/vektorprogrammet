<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class VersionTimeTable extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE school_capacity DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE school_capacity ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, CHANGE school_id school_id INT DEFAULT NULL, CHANGE semester_id semester_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE work_history ADD deletedTeamName VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE school_capacity MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE school_capacity DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE school_capacity DROP id, CHANGE school_id school_id INT NOT NULL, CHANGE semester_id semester_id INT NOT NULL');
        $this->addSql('ALTER TABLE school_capacity ADD PRIMARY KEY (school_id, semester_id)');
        $this->addSql('ALTER TABLE work_history DROP deletedTeamName');
    }
}
