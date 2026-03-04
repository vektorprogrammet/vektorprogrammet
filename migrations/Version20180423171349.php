<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180423171349 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE interview ADD coInterviewer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C348F5D2CA9 FOREIGN KEY (coInterviewer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C348F5D2CA9 ON interview (coInterviewer_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C348F5D2CA9');
        $this->addSql('DROP INDEX IDX_CF1D3C348F5D2CA9 ON interview');
        $this->addSql('ALTER TABLE interview DROP coInterviewer_id');
    }
}
