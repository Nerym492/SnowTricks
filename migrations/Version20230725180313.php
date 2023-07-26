<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230725180313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_trick (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images_trick (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, is_in_the_header TINYINT(1) NOT NULL, INDEX IDX_F5C848A2B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, group_trick_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, modification_date DATETIME DEFAULT NULL, INDEX IDX_D8F0A91EBBB1F251 (group_trick_id), INDEX IDX_D8F0A91EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, profile_photo VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(40) NOT NULL, is_verified TINYINT(1) NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, mail_confirm_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6495126AC48 (mail), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos_trick (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_6282B6A7B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE images_trick ADD CONSTRAINT FK_F5C848A2B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EBBB1F251 FOREIGN KEY (group_trick_id) REFERENCES group_trick (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE videos_trick ADD CONSTRAINT FK_6282B6A7B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE images_trick DROP FOREIGN KEY FK_F5C848A2B281BE2E');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EBBB1F251');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EA76ED395');
        $this->addSql('ALTER TABLE videos_trick DROP FOREIGN KEY FK_6282B6A7B281BE2E');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE group_trick');
        $this->addSql('DROP TABLE images_trick');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE videos_trick');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
