<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612072050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD followed_user_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ALTER category_id DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBAF2612FD FOREIGN KEY (followed_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_351268BBAF2612FD ON abonnement (followed_user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP CONSTRAINT FK_351268BBAF2612FD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_351268BBAF2612FD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP followed_user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ALTER category_id SET NOT NULL
        SQL);
    }
}
