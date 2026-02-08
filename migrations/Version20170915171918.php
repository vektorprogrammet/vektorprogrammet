<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170915171918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

	    $this->addSql('ALTER TABLE receipt ADD status VARCHAR(255) DEFAULT NULL');
	    $this->addSql('UPDATE receipt SET status="pending" WHERE active=1');
	    $this->addSql('UPDATE receipt SET status="refunded" WHERE active=0');
	    $this->addSql('ALTER TABLE receipt MODIFY status VARCHAR(255) NOT NULL, DROP active');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

	    $this->addSql('ALTER TABLE receipt ADD active TINYINT(1)');
	    $this->addSql('UPDATE receipt SET active=1');
	    $this->addSql('UPDATE receipt SET active=0 WHERE status="refunded"');
	    $this->addSql('ALTER TABLE receipt MODIFY active TINYINT(1) NOT NULL, DROP status');
    }
}
