<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420194033 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admission_subscriber (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, unsubscribeCode VARCHAR(255) NOT NULL, INDEX IDX_4F497EB7AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admission_notification (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_EBEBA8557808B1AD (subscriber_id), INDEX IDX_EBEBA8554A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admission_subscriber ADD CONSTRAINT FK_4F497EB7AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA8557808B1AD FOREIGN KEY (subscriber_id) REFERENCES admission_subscriber (id)');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA8554A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admission_notification DROP FOREIGN KEY FK_EBEBA8557808B1AD');
        $this->addSql('DROP TABLE admission_subscriber');
        $this->addSql('DROP TABLE admission_notification');
    }
}
