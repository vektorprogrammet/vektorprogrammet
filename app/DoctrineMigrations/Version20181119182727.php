<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181119182727 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD reservedFromPopUp TINYINT(1) NOT NULL DEFAULT \'0\',
                                            ADD lastPopUpTime DATETIME NOT NULL DEFAULT GETDATE()');
        $this->addSql('ALTER TABLE survey_taken ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_taken ADD CONSTRAINT FK_B3982430A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B3982430A76ED395 ON survey_taken (user_id)');
        $this->addSql('ALTER TABLE survey ADD showCustomPopUpMessage TINYINT(1) NOT NULL, ADD teamSurvey TINYINT(1) DEFAULT \'0\' NOT NULL, ADD surveyPopUpMessage LONGTEXT NOT NULL DEFAULT \'\', CHANGE showCustomFinishPage showCustomFinishPage TINYINT(1) NOT NULL DEFAULT \'0\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey DROP showCustomPopUpMessage, DROP teamSurvey, DROP surveyPopUpMessage, CHANGE showCustomFinishPage showCustomFinishPage TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE survey_taken DROP FOREIGN KEY FK_B3982430A76ED395');
        $this->addSql('DROP INDEX IDX_B3982430A76ED395 ON survey_taken');
        $this->addSql('ALTER TABLE survey_taken DROP user_id');
        $this->addSql('ALTER TABLE user DROP reservedFromPopUp, DROP lastPopUpTime');
    }
}
