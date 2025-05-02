<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502145942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id SERIAL NOT NULL, user_id_id INT NOT NULL, cible_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_351268BB9D86650F ON abonnement (user_id_id)');
        $this->addSql('CREATE INDEX IDX_351268BBA96E5E09 ON abonnement (cible_id)');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBA96E5E09 FOREIGN KEY (cible_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE abonnement DROP CONSTRAINT FK_351268BB9D86650F');
        $this->addSql('ALTER TABLE abonnement DROP CONSTRAINT FK_351268BBA96E5E09');
        $this->addSql('DROP TABLE abonnement');
    }
}
