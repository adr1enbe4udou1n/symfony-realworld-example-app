<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220118130721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_tag (article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_919694F97294869C (article_id), INDEX IDX_919694F9BAD26311 (tag_id), PRIMARY KEY(article_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE follower_user (user_id INT NOT NULL, follower_id INT NOT NULL, INDEX IDX_C60E1842A76ED395 (user_id), INDEX IDX_C60E1842AC24F853 (follower_id), PRIMARY KEY(user_id, follower_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_favorite (user_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_3D2D7AB2A76ED395 (user_id), INDEX IDX_3D2D7AB27294869C (article_id), PRIMARY KEY(user_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE article_tag');
        $this->addSql('DROP TABLE follower_user');
        $this->addSql('DROP TABLE article_favorite');
    }
}
