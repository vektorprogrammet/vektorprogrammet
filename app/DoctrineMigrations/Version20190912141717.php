<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190912141717 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE change_log_item CHANGE title title VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(1000) DEFAULT NULL, CHANGE githubLink githubLink VARCHAR(1000) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE change_log_item CHANGE title title VARCHAR(1000) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(5000) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE githubLink githubLink VARCHAR(1000) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
