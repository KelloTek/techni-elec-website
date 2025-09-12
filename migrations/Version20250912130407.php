<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912130407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id SERIAL NOT NULL, line TEXT NOT NULL, zip_code TEXT DEFAULT NULL, city TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE certification (id SERIAL NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, link TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C3C6D753DA5256D ON certification (image_id)');
        $this->addSql('CREATE TABLE discussion (id SERIAL NOT NULL, user_id INT DEFAULT NULL, request_id INT DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C0B9F90FA76ED395 ON discussion (user_id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F427EB8A5 ON discussion (request_id)');
        $this->addSql('COMMENT ON COLUMN discussion.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE document (id SERIAL NOT NULL, file_id INT DEFAULT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, request_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D8698A7693CB796C ON document (file_id)');
        $this->addSql('CREATE INDEX IDX_D8698A7612469DE2 ON document (category_id)');
        $this->addSql('CREATE INDEX IDX_D8698A76A76ED395 ON document (user_id)');
        $this->addSql('CREATE INDEX IDX_D8698A76427EB8A5 ON document (request_id)');
        $this->addSql('COMMENT ON COLUMN document.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE document_category (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE expertise (id SERIAL NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_229ADF8B3DA5256D ON expertise (image_id)');
        $this->addSql('CREATE TABLE file (id SERIAL NOT NULL, original_name TEXT NOT NULL, name TEXT NOT NULL, type VARCHAR(20) NOT NULL, size BIGINT NOT NULL, path TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE file_request (file_id INT NOT NULL, request_id INT NOT NULL, PRIMARY KEY(file_id, request_id))');
        $this->addSql('CREATE INDEX IDX_60F136A493CB796C ON file_request (file_id)');
        $this->addSql('CREATE INDEX IDX_60F136A4427EB8A5 ON file_request (request_id)');
        $this->addSql('CREATE TABLE file_realization (file_id INT NOT NULL, realization_id INT NOT NULL, PRIMARY KEY(file_id, realization_id))');
        $this->addSql('CREATE INDEX IDX_EF11CEA493CB796C ON file_realization (file_id)');
        $this->addSql('CREATE INDEX IDX_EF11CEA41A26530A ON file_realization (realization_id)');
        $this->addSql('CREATE TABLE information (id SERIAL NOT NULL, key VARCHAR(255) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_KEY ON information (key)');
        $this->addSql('CREATE TABLE notification (id SERIAL NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, link TEXT DEFAULT NULL, status BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE realization (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE request (id SERIAL NOT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B978F9F12469DE2 ON request (category_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9FA76ED395 ON request (user_id)');
        $this->addSql('COMMENT ON COLUMN request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN request.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE request_category (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reset_password_request (id SERIAL NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE to_do (id SERIAL NOT NULL, content TEXT NOT NULL, status BOOLEAN NOT NULL, before_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN to_do.before_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, address_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(20) DEFAULT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_verified BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F5B7AF75 ON users (address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_NAME ON users (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_PHONE ON users (phone)');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
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
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D753DA5256D FOREIGN KEY (image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7693CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7612469DE2 FOREIGN KEY (category_id) REFERENCES document_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expertise ADD CONSTRAINT FK_229ADF8B3DA5256D FOREIGN KEY (image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file_request ADD CONSTRAINT FK_60F136A493CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file_request ADD CONSTRAINT FK_60F136A4427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file_realization ADD CONSTRAINT FK_EF11CEA493CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file_realization ADD CONSTRAINT FK_EF11CEA41A26530A FOREIGN KEY (realization_id) REFERENCES realization (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F12469DE2 FOREIGN KEY (category_id) REFERENCES request_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE certification DROP CONSTRAINT FK_6C3C6D753DA5256D');
        $this->addSql('ALTER TABLE discussion DROP CONSTRAINT FK_C0B9F90FA76ED395');
        $this->addSql('ALTER TABLE discussion DROP CONSTRAINT FK_C0B9F90F427EB8A5');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A7693CB796C');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A7612469DE2');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A76A76ED395');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A76427EB8A5');
        $this->addSql('ALTER TABLE expertise DROP CONSTRAINT FK_229ADF8B3DA5256D');
        $this->addSql('ALTER TABLE file_request DROP CONSTRAINT FK_60F136A493CB796C');
        $this->addSql('ALTER TABLE file_request DROP CONSTRAINT FK_60F136A4427EB8A5');
        $this->addSql('ALTER TABLE file_realization DROP CONSTRAINT FK_EF11CEA493CB796C');
        $this->addSql('ALTER TABLE file_realization DROP CONSTRAINT FK_EF11CEA41A26530A');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT FK_3B978F9F12469DE2');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT FK_3B978F9FA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9F5B7AF75');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_category');
        $this->addSql('DROP TABLE expertise');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_request');
        $this->addSql('DROP TABLE file_realization');
        $this->addSql('DROP TABLE information');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE realization');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE request_category');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE to_do');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
