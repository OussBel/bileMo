<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214094204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mobile (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(255) NOT NULL, model_name VARCHAR(255) NOT NULL, operating_system VARCHAR(255) NOT NULL, cellular_technology VARCHAR(255) NOT NULL, memory_storage INT NOT NULL, connectivity_technoloy VARCHAR(255) NOT NULL, screen_size INT NOT NULL, wireless_network_technology VARCHAR(255) NOT NULL, release_date DATE NOT NULL, battery_autonomy INT NOT NULL, ram_size INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mobile');
    }
}
