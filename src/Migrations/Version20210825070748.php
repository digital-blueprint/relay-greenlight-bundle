<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Set image_original to binary.
 */
final class Version20210825070748 extends EntityManagerMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits CHANGE image_original image_original MEDIUMBLOB NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits CHANGE image_original image_original LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
