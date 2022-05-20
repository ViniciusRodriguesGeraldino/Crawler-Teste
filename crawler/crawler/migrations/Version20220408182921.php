<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408182921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_player_death_nome ON players_deaths (nome)');
        $this->addSql('CREATE INDEX idx_player_online_nome ON players_online (nome)');
        $this->addSql('CREATE INDEX idx_players_online_horas_nome ON players_online_horas (nome)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_player_death_nome ON players_deaths');
        $this->addSql('DROP INDEX idx_player_online_nome ON players_online');
        $this->addSql('DROP INDEX idx_players_online_horas_nome ON players_online_horas');
    }
}
