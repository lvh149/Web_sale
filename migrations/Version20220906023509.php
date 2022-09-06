<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906023509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2E0150FE7927C74 ON admins (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2E0150F444F97DD ON admins (phone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21E7927C74 ON customers (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21444F97DD ON customers (phone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_A2E0150FE7927C74 ON admins');
        $this->addSql('DROP INDEX UNIQ_A2E0150F444F97DD ON admins');
        $this->addSql('DROP INDEX UNIQ_3AF346685E237E06 ON categories');
        $this->addSql('DROP INDEX UNIQ_62534E21E7927C74 ON customers');
        $this->addSql('DROP INDEX UNIQ_62534E21444F97DD ON customers');
    }
}
