<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create table greenlight_permits.
 */
final class Version20210817064626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE greenlight_permits (identifier VARCHAR(50) NOT NULL, valid_from DATETIME NOT NULL, valid_until DATETIME NOT NULL, person_id VARCHAR(100) NOT NULL, place VARCHAR(255) NOT NULL, image LONGTEXT NOT NULL, consent_assurance TINYINT(1) NOT NULL, manual_check_required TINYINT(1) NOT NULL, PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE greenlight_permits');
    }
}
