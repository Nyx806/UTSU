<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612140437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP CONSTRAINT fk_bf5476ca4b89032c
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP CONSTRAINT fk_bf5476caac24f853
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf5476caac24f853
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf5476ca4b89032c
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP post_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP follower_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ALTER comment_id SET NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD post_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD follower_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD type VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ALTER comment_id DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT fk_bf5476ca4b89032c FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT fk_bf5476caac24f853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_bf5476caac24f853 ON notification (follower_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_bf5476ca4b89032c ON notification (post_id)
        SQL);
    }
}
