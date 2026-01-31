<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130091519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD outfit_id INT DEFAULT NULL, CHANGE thread_id thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('CREATE INDEX IDX_9474526CAE96E385 ON comment (outfit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CAE96E385');
        $this->addSql('DROP INDEX IDX_9474526CAE96E385 ON comment');
        $this->addSql('ALTER TABLE comment DROP outfit_id, CHANGE thread_id thread_id INT NOT NULL');
    }
}
