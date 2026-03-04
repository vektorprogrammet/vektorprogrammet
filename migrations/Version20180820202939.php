<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180820202939 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE TeamInterest (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_64833834A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teaminterest_team (teaminterest_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_381349F87077D34 (teaminterest_id), INDEX IDX_381349F8296CD8AE (team_id), PRIMARY KEY(teaminterest_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE TeamInterest ADD CONSTRAINT FK_64833834A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE teaminterest_team ADD CONSTRAINT FK_381349F87077D34 FOREIGN KEY (teaminterest_id) REFERENCES TeamInterest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teaminterest_team ADD CONSTRAINT FK_381349F8296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE teaminterest_team DROP FOREIGN KEY FK_381349F87077D34');
        $this->addSql('DROP TABLE TeamInterest');
        $this->addSql('DROP TABLE teaminterest_team');
    }
}
