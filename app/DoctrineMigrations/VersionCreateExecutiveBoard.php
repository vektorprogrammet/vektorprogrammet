<?php

namespace Application\Migrations;

use AppBundle\Entity\ExecutiveBoard;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VersionCreateExecutiveBoard extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE executive_board_member (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, board_id INT DEFAULT NULL, position LONGTEXT DEFAULT NULL, INDEX IDX_1B0352A4A76ED395 (user_id), INDEX IDX_1B0352A4E7EC5785 (board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE executive_board (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) NOT NULL, email VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, short_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE executive_board_member ADD CONSTRAINT FK_1B0352A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE executive_board_member ADD CONSTRAINT FK_1B0352A4E7EC5785 FOREIGN KEY (board_id) REFERENCES executive_board (id)');
    }

    public function postUp(Schema $schema)
    {
        $executiveBoard = new ExecutiveBoard();
        $executiveBoard->setName('Hovedstyret');
        $executiveBoard->setEmail('styret@vektorprogrammet.no');
        $executiveBoard->setShortDescription('Hovedstyret');
        $executiveBoard->setDescription('Hovedstyret');

        $em = $this->container->get('doctrine')->getManager();
        $em->persist($executiveBoard);
        $em->flush();
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE executive_board_member DROP FOREIGN KEY FK_1B0352A4E7EC5785');
        $this->addSql('DROP TABLE executive_board_member');
        $this->addSql('DROP TABLE executive_board');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
