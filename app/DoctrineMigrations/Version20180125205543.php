<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180125205543 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE application SET monday=1 WHERE monday="Bra"');
        $this->addSql('UPDATE application SET monday=0 WHERE monday="Ikke"');
        $this->addSql('UPDATE application SET monday=1 WHERE monday is NULL');

        $this->addSql('UPDATE application SET tuesday=1 WHERE tuesday="Bra"');
        $this->addSql('UPDATE application SET tuesday=0 WHERE tuesday="Ikke"');
        $this->addSql('UPDATE application SET tuesday=1 WHERE tuesday is NULL');

        $this->addSql('UPDATE application SET wednesday=1 WHERE wednesday="Bra"');
        $this->addSql('UPDATE application SET wednesday=0 WHERE wednesday="Ikke"');
        $this->addSql('UPDATE application SET wednesday=1 WHERE wednesday is NULL');


        $this->addSql('UPDATE application SET thursday=1 WHERE thursday="Bra"');
        $this->addSql('UPDATE application SET thursday=0 WHERE thursday="Ikke"');
        $this->addSql('UPDATE application SET thursday=1 WHERE thursday is NULL');

        $this->addSql('UPDATE application SET friday=1 WHERE friday="Bra"');
        $this->addSql('UPDATE application SET friday=0 WHERE friday="Ikke"');
        $this->addSql('UPDATE application SET friday=1 WHERE friday is NULL');


        $this->addSql('ALTER TABLE application
CHANGE monday monday TINYINT(1) DEFAULT \'1\' NOT NULL,
CHANGE tuesday tuesday TINYINT(1) DEFAULT \'1\' NOT NULL,
CHANGE wednesday wednesday TINYINT(1) DEFAULT \'1\' NOT NULL,
CHANGE thursday thursday TINYINT(1) DEFAULT \'1\' NOT NULL,
CHANGE friday friday TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application CHANGE monday monday VARCHAR(255) DEFAULT NULL, CHANGE tuesday tuesday VARCHAR(255) DEFAULT NULL, CHANGE wednesday wednesday VARCHAR(255) DEFAULT NULL, CHANGE thursday thursday VARCHAR(255) DEFAULT NULL, CHANGE friday friday VARCHAR(255) DEFAULT NULL');

        $this->addSql('UPDATE application SET monday="Bra" WHERE monday=1');
        $this->addSql('UPDATE application SET monday="Ikke" WHERE monday=0');

        $this->addSql('UPDATE application SET tuesday="Bra" WHERE tuesday=1');
        $this->addSql('UPDATE application SET tuesday="Ikke" WHERE tuesday=0');

        $this->addSql('UPDATE application SET wednesday="Bra" WHERE wednesday=1');
        $this->addSql('UPDATE application SET wednesday="Ikke" WHERE wednesday=0');

        $this->addSql('UPDATE application SET thursday="Bra" WHERE thursday=1');
        $this->addSql('UPDATE application SET thursday="Ikke" WHERE thursday=0');

        $this->addSql('UPDATE application SET friday="Bra" WHERE friday=1');
        $this->addSql('UPDATE application SET friday="Ikke" WHERE friday=0');
    }
}
