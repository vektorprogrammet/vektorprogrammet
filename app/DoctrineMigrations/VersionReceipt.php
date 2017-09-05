<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class VersionReceipt extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, submitDate DATETIME DEFAULT NULL, receiptDate DATETIME NOT NULL, picture_path VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, sum DOUBLE PRECISION NOT NULL, active TINYINT(1) NOT NULL, visual_id VARCHAR(255) DEFAULT NULL, INDEX IDX_5399B645A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD accountNumber VARCHAR(45) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE receipt');
        $this->addSql('ALTER TABLE user DROP accountNumber');
    }
}
