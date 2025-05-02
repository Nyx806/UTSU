<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502150727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE posts (id SERIAL NOT NULL, user_id_id INT NOT NULL, cat_id INT NOT NULL, title VARCHAR(255) NOT NULL, contenu TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, photo BYTEA DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_885DBAFA9D86650F ON posts (user_id_id)');
        $this->addSql('CREATE INDEX IDX_885DBAFAE6ADA943 ON posts (cat_id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAE6ADA943 FOREIGN KEY (cat_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFA9D86650F');
        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFAE6ADA943');
        $this->addSql('DROP TABLE posts');
    }
}
