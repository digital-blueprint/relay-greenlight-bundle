<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove place from greenlight_permits.
 */
final class Version20210819120134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits DROP place');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits ADD place VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
