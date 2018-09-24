<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180924175709 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE letter DROP FOREIGN KEY FK_8E02EE0A22DB1917');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B6922DB1917');
        $this->addSql('DROP TABLE letter');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE subscriber');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE letter (id INT AUTO_INCREMENT NOT NULL, newsletter_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, recipientCount INT NOT NULL, timestamp DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_8E02EE0A22DB1917 (newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, showOnAdmissionPage TINYINT(1) NOT NULL, INDEX IDX_7E8585C8AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber (id INT AUTO_INCREMENT NOT NULL, newsletter_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, unsubscribeCode VARCHAR(255) NOT NULL, INDEX IDX_AD005B6922DB1917 (newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE letter ADD CONSTRAINT FK_8E02EE0A22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B6922DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
    }
}
