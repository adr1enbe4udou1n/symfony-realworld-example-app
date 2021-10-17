<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211017110736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE public.articles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.comments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE public.articles (id INT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, body TEXT NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D31EA01989D9B62 ON public.articles (slug)');
        $this->addSql('CREATE INDEX IDX_7D31EA01F675F31B ON public.articles (author_id)');
        $this->addSql('CREATE TABLE public.comments (id INT NOT NULL, article_id INT DEFAULT NULL, author_id INT DEFAULT NULL, body TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9D724D437294869C ON public.comments (article_id)');
        $this->addSql('CREATE INDEX IDX_9D724D43F675F31B ON public.comments (author_id)');
        $this->addSql('CREATE TABLE public.tags (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D0531F885E237E06 ON public.tags (name)');
        $this->addSql('CREATE TABLE article_tag (tag_id INT NOT NULL, article_id INT NOT NULL, PRIMARY KEY(tag_id, article_id))');
        $this->addSql('CREATE INDEX IDX_919694F9BAD26311 ON article_tag (tag_id)');
        $this->addSql('CREATE INDEX IDX_919694F97294869C ON article_tag (article_id)');
        $this->addSql('CREATE TABLE public.users (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, bio TEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2552C48DE7927C74 ON public.users (email)');
        $this->addSql('CREATE TABLE follower_user (user_id INT NOT NULL, follower_id INT NOT NULL, PRIMARY KEY(user_id, follower_id))');
        $this->addSql('CREATE INDEX IDX_C60E1842A76ED395 ON follower_user (user_id)');
        $this->addSql('CREATE INDEX IDX_C60E1842AC24F853 ON follower_user (follower_id)');
        $this->addSql('CREATE TABLE article_favorite (user_id INT NOT NULL, article_id INT NOT NULL, PRIMARY KEY(user_id, article_id))');
        $this->addSql('CREATE INDEX IDX_3D2D7AB2A76ED395 ON article_favorite (user_id)');
        $this->addSql('CREATE INDEX IDX_3D2D7AB27294869C ON article_favorite (article_id)');
        $this->addSql('ALTER TABLE public.articles ADD CONSTRAINT FK_7D31EA01F675F31B FOREIGN KEY (author_id) REFERENCES public.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.comments ADD CONSTRAINT FK_9D724D437294869C FOREIGN KEY (article_id) REFERENCES public.articles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.comments ADD CONSTRAINT FK_9D724D43F675F31B FOREIGN KEY (author_id) REFERENCES public.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F9BAD26311 FOREIGN KEY (tag_id) REFERENCES public.tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F97294869C FOREIGN KEY (article_id) REFERENCES public.articles (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE follower_user ADD CONSTRAINT FK_C60E1842A76ED395 FOREIGN KEY (user_id) REFERENCES public.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE follower_user ADD CONSTRAINT FK_C60E1842AC24F853 FOREIGN KEY (follower_id) REFERENCES public.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_favorite ADD CONSTRAINT FK_3D2D7AB2A76ED395 FOREIGN KEY (user_id) REFERENCES public.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_favorite ADD CONSTRAINT FK_3D2D7AB27294869C FOREIGN KEY (article_id) REFERENCES public.articles (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE public.comments DROP CONSTRAINT FK_9D724D437294869C');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_919694F97294869C');
        $this->addSql('ALTER TABLE article_favorite DROP CONSTRAINT FK_3D2D7AB27294869C');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_919694F9BAD26311');
        $this->addSql('ALTER TABLE public.articles DROP CONSTRAINT FK_7D31EA01F675F31B');
        $this->addSql('ALTER TABLE public.comments DROP CONSTRAINT FK_9D724D43F675F31B');
        $this->addSql('ALTER TABLE follower_user DROP CONSTRAINT FK_C60E1842A76ED395');
        $this->addSql('ALTER TABLE follower_user DROP CONSTRAINT FK_C60E1842AC24F853');
        $this->addSql('ALTER TABLE article_favorite DROP CONSTRAINT FK_3D2D7AB2A76ED395');
        $this->addSql('DROP SEQUENCE public.articles_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.comments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.tags_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.users_id_seq CASCADE');
        $this->addSql('DROP TABLE public.articles');
        $this->addSql('DROP TABLE public.comments');
        $this->addSql('DROP TABLE public.tags');
        $this->addSql('DROP TABLE article_tag');
        $this->addSql('DROP TABLE public.users');
        $this->addSql('DROP TABLE follower_user');
        $this->addSql('DROP TABLE article_favorite');
    }
}
