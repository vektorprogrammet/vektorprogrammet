<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261104170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store question display order per interview schema without replacing question IDs.';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('interview_schema');
        $table->addColumn('question_order', 'json', ['notnull' => false]);
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('interview_schema')->dropColumn('question_order');
    }
}
