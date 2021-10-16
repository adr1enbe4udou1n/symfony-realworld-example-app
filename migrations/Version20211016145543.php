<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211016145543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE public.user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE public."user" (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, bio TEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_327C5DE7E7927C74 ON public."user" (email)');
        $this->addSql('CREATE TABLE follower_user (user_id INT NOT NULL, follower_id INT NOT NULL, PRIMARY KEY(user_id, follower_id))');
        $this->addSql('CREATE INDEX IDX_C60E1842A76ED395 ON follower_user (user_id)');
        $this->addSql('CREATE INDEX IDX_C60E1842AC24F853 ON follower_user (follower_id)');
        $this->addSql('ALTER TABLE follower_user ADD CONSTRAINT FK_C60E1842A76ED395 FOREIGN KEY (user_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE follower_user ADD CONSTRAINT FK_C60E1842AC24F853 FOREIGN KEY (follower_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE follower_user DROP CONSTRAINT FK_C60E1842A76ED395');
        $this->addSql('ALTER TABLE follower_user DROP CONSTRAINT FK_C60E1842AC24F853');
        $this->addSql('DROP SEQUENCE public.user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE public."user"');
        $this->addSql('DROP TABLE follower_user');
    }
}
