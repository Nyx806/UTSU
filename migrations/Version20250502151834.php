<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502151834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaires (id SERIAL NOT NULL, post_id INT NOT NULL, com_parent_id INT DEFAULT NULL, contenu TEXT NOT NULL, img BYTEA DEFAULT NULL, video BYTEA DEFAULT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D9BEC0C44B89032C ON commentaires (post_id)');
        $this->addSql('CREATE INDEX IDX_D9BEC0C4A1A7DB4F ON commentaires (com_parent_id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C44B89032C FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4A1A7DB4F FOREIGN KEY (com_parent_id) REFERENCES commentaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commentaires DROP CONSTRAINT FK_D9BEC0C44B89032C');
        $this->addSql('ALTER TABLE commentaires DROP CONSTRAINT FK_D9BEC0C4A1A7DB4F');
        $this->addSql('DROP TABLE commentaires');
    }
}
