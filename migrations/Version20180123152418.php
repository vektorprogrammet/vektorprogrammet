<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180123152418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application ADD language VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE application SET language="Norsk" WHERE english=0');
        $this->addSql('UPDATE application SET language="Engelsk" WHERE english=1');
        $this->addSql('ALTER TABLE application  DROP english');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application ADD english TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('UPDATE application SET english=1 WHERE language="Engelsk"');
        $this->addSql('UPDATE application SET english=0 WHERE language="Norsk"');
        $this->addSql('UPDATE application SET english=1 WHERE language="Norsk og engelsk"');
        $this->addSql('ALTER TABLE application DROP language');
    }
}
