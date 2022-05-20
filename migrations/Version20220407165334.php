<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407165334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE players_online (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, data_consulta DATETIME DEFAULT NULL, total_horas_online INT DEFAULT NULL, level INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players_online_horas (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, data_online DATE DEFAULT NULL, hora_online TIME NOT NULL, hora_offline TIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE players_online');
        $this->addSql('DROP TABLE players_online_horas');
    }
}
