<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181014003008 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE unhandled_access_rule (id INT AUTO_INCREMENT NOT NULL, resource VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access_rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, resource VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL, isRoutingRule TINYINT(1) NOT NULL, forExecutiveBoard TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accessrule_user (accessrule_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9D820D6F7FF671E0 (accessrule_id), INDEX IDX_9D820D6FA76ED395 (user_id), PRIMARY KEY(accessrule_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accessrule_team (accessrule_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_D4F17D397FF671E0 (accessrule_id), INDEX IDX_D4F17D39296CD8AE (team_id), PRIMARY KEY(accessrule_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accessrule_role (accessrule_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_4778514C7FF671E0 (accessrule_id), INDEX IDX_4778514CD60322AC (role_id), PRIMARY KEY(accessrule_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accessrule_user ADD CONSTRAINT FK_9D820D6F7FF671E0 FOREIGN KEY (accessrule_id) REFERENCES access_rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accessrule_user ADD CONSTRAINT FK_9D820D6FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accessrule_team ADD CONSTRAINT FK_D4F17D397FF671E0 FOREIGN KEY (accessrule_id) REFERENCES access_rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accessrule_team ADD CONSTRAINT FK_D4F17D39296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accessrule_role ADD CONSTRAINT FK_4778514C7FF671E0 FOREIGN KEY (accessrule_id) REFERENCES access_rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE accessrule_role ADD CONSTRAINT FK_4778514CD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE accessrule_user DROP FOREIGN KEY FK_9D820D6F7FF671E0');
        $this->addSql('ALTER TABLE accessrule_team DROP FOREIGN KEY FK_D4F17D397FF671E0');
        $this->addSql('ALTER TABLE accessrule_role DROP FOREIGN KEY FK_4778514C7FF671E0');
        $this->addSql('DROP TABLE unhandled_access_rule');
        $this->addSql('DROP TABLE access_rule');
        $this->addSql('DROP TABLE accessrule_user');
        $this->addSql('DROP TABLE accessrule_team');
        $this->addSql('DROP TABLE accessrule_role');
    }
}
