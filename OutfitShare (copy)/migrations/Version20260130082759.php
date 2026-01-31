<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130082759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, thread_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526CE2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saved_outfit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, outfit_id INT NOT NULL, saved_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5604ED1AA76ED395 (user_id), INDEX IDX_5604ED1AAE96E385 (outfit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_31204C83F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE saved_outfit ADD CONSTRAINT FK_5604ED1AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE saved_outfit ADD CONSTRAINT FK_5604ED1AAE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C83F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outfit ADD accessories JSON NOT NULL, ADD type VARCHAR(255) DEFAULT \'User Generated\' NOT NULL');
        $this->addSql('ALTER TABLE prenda ADD size VARCHAR(50) DEFAULT NULL, ADD price NUMERIC(10, 2) DEFAULT NULL, ADD purchase_date DATE DEFAULT NULL, ADD season VARCHAR(255) DEFAULT NULL, ADD condition_state VARCHAR(100) DEFAULT NULL, ADD fabric_type VARCHAR(100) DEFAULT NULL, ADD care_instructions LONGTEXT DEFAULT NULL, ADD smart_tags JSON NOT NULL, ADD purchase_link VARCHAR(255) DEFAULT NULL, ADD no_bg_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD google_id VARCHAR(255) DEFAULT NULL, ADD nickname VARCHAR(50) DEFAULT NULL, ADD birthdate DATE DEFAULT NULL, ADD style_preference VARCHAR(255) DEFAULT NULL, ADD biography LONGTEXT DEFAULT NULL, ADD profile_photo VARCHAR(255) DEFAULT NULL, ADD banner_photo VARCHAR(255) DEFAULT NULL, ADD is_public TINYINT(1) DEFAULT 0 NOT NULL, ADD personality_traits JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64976F5C865 ON user (google_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A188FE64 ON user (nickname)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE2904019');
        $this->addSql('ALTER TABLE saved_outfit DROP FOREIGN KEY FK_5604ED1AA76ED395');
        $this->addSql('ALTER TABLE saved_outfit DROP FOREIGN KEY FK_5604ED1AAE96E385');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C83F675F31B');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE saved_outfit');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP INDEX UNIQ_8D93D64976F5C865 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649A188FE64 ON user');
        $this->addSql('ALTER TABLE user DROP google_id, DROP nickname, DROP birthdate, DROP style_preference, DROP biography, DROP profile_photo, DROP banner_photo, DROP is_public, DROP personality_traits');
        $this->addSql('ALTER TABLE outfit DROP accessories, DROP type');
        $this->addSql('ALTER TABLE prenda DROP size, DROP price, DROP purchase_date, DROP season, DROP condition_state, DROP fabric_type, DROP care_instructions, DROP smart_tags, DROP purchase_link, DROP no_bg_image');
    }
}
