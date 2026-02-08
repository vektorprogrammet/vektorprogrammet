<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190117172903 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE todo_item (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, semester_id INT DEFAULT NULL, createdAt DATE NOT NULL, deletedAt DATE DEFAULT NULL, priority INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(5000) NOT NULL, INDEX IDX_40CA4301AE80F5DF (department_id), INDEX IDX_40CA43014A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE todo_mandatory (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, isMandatory TINYINT(1) NOT NULL, todoItem_id INT DEFAULT NULL, INDEX IDX_6549295B4D6A1963 (todoItem_id), INDEX IDX_6549295B4A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE todo_completed (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, department_id INT DEFAULT NULL, completedAt DATE NOT NULL, todoItem_id INT DEFAULT NULL, INDEX IDX_776DEA7E4D6A1963 (todoItem_id), INDEX IDX_776DEA7E4A798B6F (semester_id), INDEX IDX_776DEA7EAE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE todo_deadline (id INT AUTO_INCREMENT NOT NULL, semester_id INT DEFAULT NULL, deadDate DATE NOT NULL, todoItem_id INT DEFAULT NULL, INDEX IDX_90903C924D6A1963 (todoItem_id), INDEX IDX_90903C924A798B6F (semester_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE todo_item ADD CONSTRAINT FK_40CA4301AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE todo_item ADD CONSTRAINT FK_40CA43014A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE todo_mandatory ADD CONSTRAINT FK_6549295B4D6A1963 FOREIGN KEY (todoItem_id) REFERENCES todo_item (id)');
        $this->addSql('ALTER TABLE todo_mandatory ADD CONSTRAINT FK_6549295B4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE todo_completed ADD CONSTRAINT FK_776DEA7E4D6A1963 FOREIGN KEY (todoItem_id) REFERENCES todo_item (id)');
        $this->addSql('ALTER TABLE todo_completed ADD CONSTRAINT FK_776DEA7E4A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
        $this->addSql('ALTER TABLE todo_completed ADD CONSTRAINT FK_776DEA7EAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE todo_deadline ADD CONSTRAINT FK_90903C924D6A1963 FOREIGN KEY (todoItem_id) REFERENCES todo_item (id)');
        $this->addSql('ALTER TABLE todo_deadline ADD CONSTRAINT FK_90903C924A798B6F FOREIGN KEY (semester_id) REFERENCES semester (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE todo_mandatory DROP FOREIGN KEY FK_6549295B4D6A1963');
        $this->addSql('ALTER TABLE todo_completed DROP FOREIGN KEY FK_776DEA7E4D6A1963');
        $this->addSql('ALTER TABLE todo_deadline DROP FOREIGN KEY FK_90903C924D6A1963');
        $this->addSql('DROP TABLE todo_item');
        $this->addSql('DROP TABLE todo_mandatory');
        $this->addSql('DROP TABLE todo_completed');
        $this->addSql('DROP TABLE todo_deadline');
    }
}
