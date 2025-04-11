<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411133356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE posts_commentaire (posts_id INT NOT NULL, commentaire_id INT NOT NULL, PRIMARY KEY(posts_id, commentaire_id))');
        $this->addSql('CREATE INDEX IDX_BBB4559FD5E258C5 ON posts_commentaire (posts_id)');
        $this->addSql('CREATE INDEX IDX_BBB4559FBA9CD190 ON posts_commentaire (commentaire_id)');
        $this->addSql('CREATE TABLE posts_likes (posts_id INT NOT NULL, likes_id INT NOT NULL, PRIMARY KEY(posts_id, likes_id))');
        $this->addSql('CREATE INDEX IDX_FBCD156CD5E258C5 ON posts_likes (posts_id)');
        $this->addSql('CREATE INDEX IDX_FBCD156C2F23775F ON posts_likes (likes_id)');
        $this->addSql('ALTER TABLE posts_commentaire ADD CONSTRAINT FK_BBB4559FD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_commentaire ADD CONSTRAINT FK_BBB4559FBA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_likes ADD CONSTRAINT FK_FBCD156CD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_likes ADD CONSTRAINT FK_FBCD156C2F23775F FOREIGN KEY (likes_id) REFERENCES likes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE posts_commentaire DROP CONSTRAINT FK_BBB4559FD5E258C5');
        $this->addSql('ALTER TABLE posts_commentaire DROP CONSTRAINT FK_BBB4559FBA9CD190');
        $this->addSql('ALTER TABLE posts_likes DROP CONSTRAINT FK_FBCD156CD5E258C5');
        $this->addSql('ALTER TABLE posts_likes DROP CONSTRAINT FK_FBCD156C2F23775F');
        $this->addSql('DROP TABLE posts_commentaire');
        $this->addSql('DROP TABLE posts_likes');
    }
}
