<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411163048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id SERIAL NOT NULL, follower_id INT NOT NULL, utilisateur_id INT NOT NULL, cible_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE categories (id SERIAL NOT NULL, categorie_id INT NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE commentaire (id SERIAL NOT NULL, com_id INT NOT NULL, id_post INT NOT NULL, id_com_parent INT NOT NULL, contenu TEXT NOT NULL, date_creation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE likes (id SERIAL NOT NULL, user_like_id INT DEFAULT NULL, user_id INT NOT NULL, post_id INT NOT NULL, cat_id INT NOT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_49CA4E7DDD96E438 ON likes (user_like_id)');
        $this->addSql('CREATE TABLE posts (id SERIAL NOT NULL, poster_id INT NOT NULL, categories_id INT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, cat_id INT NOT NULL, title TEXT NOT NULL, contenu TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, photo BYTEA DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_885DBAFA5BB66C05 ON posts (poster_id)');
        $this->addSql('CREATE INDEX IDX_885DBAFAA21214B7 ON posts (categories_id)');
        $this->addSql('CREATE TABLE posts_commentaire (posts_id INT NOT NULL, commentaire_id INT NOT NULL, PRIMARY KEY(posts_id, commentaire_id))');
        $this->addSql('CREATE INDEX IDX_BBB4559FD5E258C5 ON posts_commentaire (posts_id)');
        $this->addSql('CREATE INDEX IDX_BBB4559FBA9CD190 ON posts_commentaire (commentaire_id)');
        $this->addSql('CREATE TABLE posts_likes (posts_id INT NOT NULL, likes_id INT NOT NULL, PRIMARY KEY(posts_id, likes_id))');
        $this->addSql('CREATE INDEX IDX_FBCD156CD5E258C5 ON posts_likes (posts_id)');
        $this->addSql('CREATE INDEX IDX_FBCD156C2F23775F ON posts_likes (likes_id)');
        $this->addSql('CREATE TABLE tableau (id SERIAL NOT NULL, tableau_id INT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, cat_id INT NOT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pp_image BYTEA NOT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE user_abonnement (user_id INT NOT NULL, abonnement_id INT NOT NULL, PRIMARY KEY(user_id, abonnement_id))');
        $this->addSql('CREATE INDEX IDX_9275AE57A76ED395 ON user_abonnement (user_id)');
        $this->addSql('CREATE INDEX IDX_9275AE57F1D74413 ON user_abonnement (abonnement_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DDD96E438 FOREIGN KEY (user_like_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA5BB66C05 FOREIGN KEY (poster_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_commentaire ADD CONSTRAINT FK_BBB4559FD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_commentaire ADD CONSTRAINT FK_BBB4559FBA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_likes ADD CONSTRAINT FK_FBCD156CD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_likes ADD CONSTRAINT FK_FBCD156C2F23775F FOREIGN KEY (likes_id) REFERENCES likes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_abonnement ADD CONSTRAINT FK_9275AE57A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_abonnement ADD CONSTRAINT FK_9275AE57F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE likes DROP CONSTRAINT FK_49CA4E7DDD96E438');
        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFA5BB66C05');
        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFAA21214B7');
        $this->addSql('ALTER TABLE posts_commentaire DROP CONSTRAINT FK_BBB4559FD5E258C5');
        $this->addSql('ALTER TABLE posts_commentaire DROP CONSTRAINT FK_BBB4559FBA9CD190');
        $this->addSql('ALTER TABLE posts_likes DROP CONSTRAINT FK_FBCD156CD5E258C5');
        $this->addSql('ALTER TABLE posts_likes DROP CONSTRAINT FK_FBCD156C2F23775F');
        $this->addSql('ALTER TABLE user_abonnement DROP CONSTRAINT FK_9275AE57A76ED395');
        $this->addSql('ALTER TABLE user_abonnement DROP CONSTRAINT FK_9275AE57F1D74413');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE posts_commentaire');
        $this->addSql('DROP TABLE posts_likes');
        $this->addSql('DROP TABLE tableau');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_abonnement');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
