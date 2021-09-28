<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add unique index on person_id.
 */
final class Version20210928095618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits ADD UNIQUE idxPersonId (person_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP index idxPersonId ON greenlight_permits');
    }
}
