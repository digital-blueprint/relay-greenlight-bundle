<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add image handling columns.
 */
final class Version20210824130640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits ADD image_generated LONGTEXT NOT NULL, ADD input_hash VARCHAR(64) NOT NULL, CHANGE image image_original LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits ADD image LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP image_original, DROP image_generated, DROP input_hash');
    }
}
