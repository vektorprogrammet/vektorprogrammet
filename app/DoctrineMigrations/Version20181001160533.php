<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181001160533 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD reserved_pop_up TINYINT(1) DEFAULT NULL, ADD last_pop_up DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_taken ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_taken ADD CONSTRAINT FK_B3982430A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B3982430A76ED395 ON survey_taken (user_id)');
        $this->addSql('ALTER TABLE survey ADD teamSurvey TINYINT(1) DEFAULT \'0\' NOT NULL, ADD surveyPopUpMessage LONGTEXT DEFAULT \'Vi har en undersøkelse klar til deg!\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey DROP teamSurvey, DROP surveyPopUpMessage');
        $this->addSql('ALTER TABLE survey_taken DROP FOREIGN KEY FK_B3982430A76ED395');
        $this->addSql('DROP INDEX IDX_B3982430A76ED395 ON survey_taken');
        $this->addSql('ALTER TABLE survey_taken DROP user_id');
        $this->addSql('ALTER TABLE user DROP reserved_pop_up, DROP last_pop_up');
    }
}
