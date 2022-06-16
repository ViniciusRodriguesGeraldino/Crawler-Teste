<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408182522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX players_nome_index ON players');
        $this->addSql('ALTER TABLE players ADD data_acc_criada DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX players_online_deaths_nome_index ON players_deaths');
        $this->addSql('DROP INDEX players_online_nome_index ON players_online');
        $this->addSql('DROP INDEX players_onlie_horas_nome_index ON players_online_horas');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE players DROP data_acc_criada');
        $this->addSql('CREATE INDEX players_nome_index ON players (nome)');
        $this->addSql('CREATE INDEX players_online_deaths_nome_index ON players_deaths (nome)');
        $this->addSql('CREATE INDEX players_online_nome_index ON players_online (nome)');
        $this->addSql('CREATE INDEX players_onlie_horas_nome_index ON players_online_horas (nome)');
    }
}
