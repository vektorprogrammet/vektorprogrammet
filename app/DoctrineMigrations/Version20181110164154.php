<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181110164154 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5B4E7AF8F');
        $this->addSql('DROP TABLE Image');
        $this->addSql('DROP TABLE ImageGallery');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Image (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, description VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_4FC2B5B4E7AF8F (gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ImageGallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, referenceName VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_C689231577B2FEC3 (referenceName), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Image ADD CONSTRAINT FK_4FC2B5B4E7AF8F FOREIGN KEY (gallery_id) REFERENCES ImageGallery (id)');
    }
}
