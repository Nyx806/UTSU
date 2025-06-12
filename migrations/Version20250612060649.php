<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612060649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE report_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report DROP CONSTRAINT fk_c42f7784e1cfe6f5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report DROP CONSTRAINT fk_c42f7784e7566e
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE report
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP CONSTRAINT fk_351268bbaf2612fd
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_351268bbaf2612fd
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP followed_user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ALTER category_id SET NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE report (id SERIAL NOT NULL, reporter_id INT NOT NULL, reported_user_id INT NOT NULL, reason TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_c42f7784e7566e ON report (reported_user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_c42f7784e1cfe6f5 ON report (reporter_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD CONSTRAINT fk_c42f7784e1cfe6f5 FOREIGN KEY (reporter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD CONSTRAINT fk_c42f7784e7566e FOREIGN KEY (reported_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD followed_user_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ALTER category_id DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT fk_351268bbaf2612fd FOREIGN KEY (followed_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_351268bbaf2612fd ON abonnement (followed_user_id)
        SQL);
    }
}
