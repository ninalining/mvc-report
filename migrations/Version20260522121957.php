<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260522121957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE game_round (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, player_id INTEGER NOT NULL, bet INTEGER NOT NULL, result VARCHAR(20) NOT NULL, player_score INTEGER NOT NULL, dealer_score INTEGER NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_F7DD93BB99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F7DD93BB99E6F5DF ON game_round (player_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE player (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, balance INTEGER NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_98197A655E237E06 ON player (name)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE game_round
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE player
        SQL);
    }
}
