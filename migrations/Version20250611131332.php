<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611131332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP CONSTRAINT fk_351268bba96e5e09');
        $this->addSql('DROP INDEX idx_351268bba96e5e09');
        $this->addSql('ALTER TABLE abonnement RENAME COLUMN cible_id TO category_id');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_351268BB12469DE2 ON abonnement (category_id)');
        $this->addSql('ALTER TABLE categories ALTER dangerous DROP DEFAULT');
        $this->addSql('ALTER TABLE posts ALTER dangerous DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categories ALTER dangerous SET DEFAULT 0');
        $this->addSql('ALTER TABLE abonnement DROP CONSTRAINT FK_351268BB12469DE2');
        $this->addSql('DROP INDEX IDX_351268BB12469DE2');
        $this->addSql('ALTER TABLE abonnement RENAME COLUMN category_id TO cible_id');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT fk_351268bba96e5e09 FOREIGN KEY (cible_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_351268bba96e5e09 ON abonnement (cible_id)');
        $this->addSql('ALTER TABLE posts ALTER dangerous SET DEFAULT 0');
    }
}
