<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180120221748 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE infomeeting (id INT AUTO_INCREMENT NOT NULL, showOnPage TINYINT(1) DEFAULT NULL, date DATETIME DEFAULT NULL, room VARCHAR(250) DEFAULT NULL, description VARCHAR(250) DEFAULT NULL, link VARCHAR(250) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE semester ADD infoMeeting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDC8F2226C FOREIGN KEY (infoMeeting_id) REFERENCES infomeeting (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7388EEDC8F2226C ON semester (infoMeeting_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDC8F2226C');
        $this->addSql('DROP TABLE infomeeting');
        $this->addSql('DROP INDEX UNIQ_F7388EEDC8F2226C ON semester');
        $this->addSql('ALTER TABLE semester DROP infoMeeting_id');
    }
}
