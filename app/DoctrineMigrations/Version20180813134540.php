<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180813134540 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assistant_history DROP FOREIGN KEY FK_1B31A1DBA76ED395');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645A76ED395');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE executive_board_membership DROP FOREIGN KEY FK_F6490587A76ED395');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE certificate_request DROP FOREIGN KEY FK_6E343C40A76ED395');
        $this->addSql('ALTER TABLE certificate_request ADD CONSTRAINT FK_6E343C40A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview_question_alternative DROP FOREIGN KEY FK_711360B41E27F6BF');
        $this->addSql('ALTER TABLE interview_question_alternative ADD CONSTRAINT FK_711360B41E27F6BF FOREIGN KEY (question_id) REFERENCES interview_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE signature DROP FOREIGN KEY FK_AE880141A76ED395');
        $this->addSql('ALTER TABLE signature ADD CONSTRAINT FK_AE880141A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C347906D9E8');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C348F5D2CA9');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34A76ED395');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C347906D9E8 FOREIGN KEY (interviewer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C348F5D2CA9 FOREIGN KEY (coInterviewer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assistant_history DROP FOREIGN KEY FK_1B31A1DBA76ED395');
        $this->addSql('ALTER TABLE assistant_history ADD CONSTRAINT FK_1B31A1DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certificate_request DROP FOREIGN KEY FK_6E343C40A76ED395');
        $this->addSql('ALTER TABLE certificate_request ADD CONSTRAINT FK_6E343C40A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE executive_board_membership DROP FOREIGN KEY FK_F6490587A76ED395');
        $this->addSql('ALTER TABLE executive_board_membership ADD CONSTRAINT FK_F6490587A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C347906D9E8');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C348F5D2CA9');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34A76ED395');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C347906D9E8 FOREIGN KEY (interviewer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C348F5D2CA9 FOREIGN KEY (coInterviewer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interview_question_alternative DROP FOREIGN KEY FK_711360B41E27F6BF');
        $this->addSql('ALTER TABLE interview_question_alternative ADD CONSTRAINT FK_711360B41E27F6BF FOREIGN KEY (question_id) REFERENCES interview_question (id)');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645A76ED395');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE signature DROP FOREIGN KEY FK_AE880141A76ED395');
        $this->addSql('ALTER TABLE signature ADD CONSTRAINT FK_AE880141A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
