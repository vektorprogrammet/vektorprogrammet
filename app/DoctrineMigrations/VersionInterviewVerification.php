<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class VersionInterviewVerification extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE interview ADD interviewStatus INT DEFAULT NULL, ADD responseCode VARCHAR(255) DEFAULT NULL, ADD cancelMessage VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE interview SET interviewStatus=1 WHERE cancelled=0');
        $this->addSql('UPDATE interview SET interviewStatus=3 WHERE cancelled=1');
        $this->addSql('ALTER TABLE interview MODIFY interviewStatus INT NOT NULL, DROP cancelled');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE interview ADD cancelled TINYINT(1)');
        $this->addSql('UPDATE interview SET cancelled=0');
        $this->addSql('UPDATE interview SET cancelled=1 WHERE interviewStatus=3');
        $this->addSql('ALTER TABLE interview MODIFY cancelled TINYINT(1) NOT NULL, DROP interviewStatus, DROP responseCode, DROP cancelMessage');
    }
}
