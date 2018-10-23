<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181014190516 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
	    $this->addSql("UPDATE role SET name='Bruker' WHERE role = 'ROLE_USER'");
	    $this->addSql("UPDATE role SET role = 'ROLE_TEAM_MEMBER', name='Teammedlem' WHERE role = 'ROLE_ADMIN'");
	    $this->addSql("UPDATE role SET role = 'ROLE_TEAM_LEADER', name='Teamleder' WHERE role = 'ROLE_SUPER_ADMIN'");
	    $this->addSql("UPDATE role SET role = 'ROLE_ADMIN', name='Admin' WHERE role = 'ROLE_HIGHEST_ADMIN'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
	    $this->addSql("UPDATE role SET name='User' WHERE role = 'ROLE_USER'");
	    $this->addSql("UPDATE role SET role = 'ROLE_SUPER_ADMIN', name='Superadmin' WHERE role = 'ROLE_TEAM_LEADER'");
	    $this->addSql("UPDATE role SET role = 'ROLE_HIGHEST_ADMIN', name='Highest admin' WHERE role = 'ROLE_ADMIN'");
	    $this->addSql("UPDATE role SET role = 'ROLE_ADMIN', name='Admin' WHERE role = 'ROLE_TEAM_MEMBER'");
    }
}
