<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove manual_check_required and add additional_information.
 */
final class Version20210826083553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits ADD additional_information VARCHAR(255) NOT NULL, DROP manual_check_required');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE greenlight_permits ADD manual_check_required TINYINT(1) NOT NULL, DROP additional_information');
    }
}
