<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181020131347 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE school_capacity ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE school_capacity ADD CONSTRAINT FK_4BAE8530AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_4BAE8530AE80F5DF ON school_capacity (department_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE school_capacity DROP FOREIGN KEY FK_4BAE8530AE80F5DF');
        $this->addSql('DROP INDEX IDX_4BAE8530AE80F5DF ON school_capacity');
        $this->addSql('ALTER TABLE school_capacity DROP department_id');
    }
}
