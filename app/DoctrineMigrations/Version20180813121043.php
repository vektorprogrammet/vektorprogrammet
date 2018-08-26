<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180813121043 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admission_notification DROP FOREIGN KEY FK_EBEBA8557808B1AD');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA8557808B1AD FOREIGN KEY (subscriber_id) REFERENCES admission_subscriber (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admission_notification DROP FOREIGN KEY FK_EBEBA8557808B1AD');
        $this->addSql('ALTER TABLE admission_notification ADD CONSTRAINT FK_EBEBA8557808B1AD FOREIGN KEY (subscriber_id) REFERENCES admission_subscriber (id)');
    }
}
