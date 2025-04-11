<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411132415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_abonnement (user_id INT NOT NULL, abonnement_id INT NOT NULL, PRIMARY KEY(user_id, abonnement_id))');
        $this->addSql('CREATE INDEX IDX_9275AE57A76ED395 ON user_abonnement (user_id)');
        $this->addSql('CREATE INDEX IDX_9275AE57F1D74413 ON user_abonnement (abonnement_id)');
        $this->addSql('ALTER TABLE user_abonnement ADD CONSTRAINT FK_9275AE57A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_abonnement ADD CONSTRAINT FK_9275AE57F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_abonnement DROP CONSTRAINT FK_9275AE57A76ED395');
        $this->addSql('ALTER TABLE user_abonnement DROP CONSTRAINT FK_9275AE57F1D74413');
        $this->addSql('DROP TABLE user_abonnement');
    }
}
