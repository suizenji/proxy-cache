<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221094535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE cache_rule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE http_body_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE http_context_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE http_header_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE cache_rule (id INT NOT NULL, judge_type VARCHAR(255) NOT NULL, judge_cond VARCHAR(255) NOT NULL, res_type VARCHAR(255) NOT NULL, res_cond VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE http_body (id INT NOT NULL, tran_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, content BYTEA NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE http_context (id INT NOT NULL, tran_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, f1 VARCHAR(255) NOT NULL, f2 VARCHAR(255) NOT NULL, f3 VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN http_context.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE http_header (id INT NOT NULL, tran_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(1024) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE cache_rule_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE http_body_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE http_context_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE http_header_id_seq CASCADE');
        $this->addSql('DROP TABLE cache_rule');
        $this->addSql('DROP TABLE http_body');
        $this->addSql('DROP TABLE http_context');
        $this->addSql('DROP TABLE http_header');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
