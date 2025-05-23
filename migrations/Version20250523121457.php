<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523121457 extends AbstractMigration
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
        $this->addSql('CREATE TABLE categories (id SERIAL NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE commentaires (id SERIAL NOT NULL, post_id INT NOT NULL, com_parent_id INT DEFAULT NULL, user_id_id INT NOT NULL, contenu TEXT NOT NULL, img TEXT DEFAULT NULL, video BYTEA DEFAULT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D9BEC0C44B89032C ON commentaires (post_id)');
        $this->addSql('CREATE INDEX IDX_D9BEC0C4A1A7DB4F ON commentaires (com_parent_id)');
        $this->addSql('CREATE INDEX IDX_D9BEC0C49D86650F ON commentaires (user_id_id)');
        $this->addSql('CREATE TABLE likes (id SERIAL NOT NULL, user_id_id INT NOT NULL, post_id INT NOT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_49CA4E7D9D86650F ON likes (user_id_id)');
        $this->addSql('CREATE INDEX IDX_49CA4E7D4B89032C ON likes (post_id)');
        $this->addSql('CREATE TABLE posts (id SERIAL NOT NULL, user_id_id INT NOT NULL, cat_id INT NOT NULL, title VARCHAR(255) NOT NULL, contenu TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, photo TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_885DBAFA9D86650F ON posts (user_id_id)');
        $this->addSql('CREATE INDEX IDX_885DBAFAE6ADA943 ON posts (cat_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pp_img TEXT NOT NULL, type INT NOT NULL, username VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
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
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBA96E5E09 FOREIGN KEY (cible_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C44B89032C FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4A1A7DB4F FOREIGN KEY (com_parent_id) REFERENCES commentaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C49D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D4B89032C FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAE6ADA943 FOREIGN KEY (cat_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE abonnement DROP CONSTRAINT FK_351268BB9D86650F');
        $this->addSql('ALTER TABLE abonnement DROP CONSTRAINT FK_351268BBA96E5E09');
        $this->addSql('ALTER TABLE commentaires DROP CONSTRAINT FK_D9BEC0C44B89032C');
        $this->addSql('ALTER TABLE commentaires DROP CONSTRAINT FK_D9BEC0C4A1A7DB4F');
        $this->addSql('ALTER TABLE commentaires DROP CONSTRAINT FK_D9BEC0C49D86650F');
        $this->addSql('ALTER TABLE likes DROP CONSTRAINT FK_49CA4E7D9D86650F');
        $this->addSql('ALTER TABLE likes DROP CONSTRAINT FK_49CA4E7D4B89032C');
        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFA9D86650F');
        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFAE6ADA943');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
