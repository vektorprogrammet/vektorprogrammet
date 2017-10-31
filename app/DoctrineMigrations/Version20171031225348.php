<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171031225348 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Image (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_4FC2B5B4E7AF8F (gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ImageGallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, referenceName VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, filters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_C689231577B2FEC3 (referenceName), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Image ADD CONSTRAINT FK_4FC2B5B4E7AF8F FOREIGN KEY (gallery_id) REFERENCES ImageGallery (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5B4E7AF8F');
        $this->addSql('DROP TABLE Image');
        $this->addSql('DROP TABLE ImageGallery');
    }
}
