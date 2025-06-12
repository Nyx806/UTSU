<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612231135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category_likes (id SERIAL NOT NULL, user_id_id INT NOT NULL, category_id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A1F901349D86650F ON category_likes (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A1F9013412469DE2 ON category_likes (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_likes ADD CONSTRAINT FK_A1F901349D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_likes ADD CONSTRAINT FK_A1F9013412469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_likes DROP CONSTRAINT FK_A1F901349D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_likes DROP CONSTRAINT FK_A1F9013412469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category_likes
        SQL);
    }
}
