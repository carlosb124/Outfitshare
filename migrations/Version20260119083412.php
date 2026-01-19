<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119083412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE outfit_prenda (outfit_id INT NOT NULL, prenda_id INT NOT NULL, INDEX IDX_F8FE7B98AE96E385 (outfit_id), INDEX IDX_F8FE7B9865871137 (prenda_id), PRIMARY KEY(outfit_id, prenda_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outfit_prenda ADD CONSTRAINT FK_F8FE7B98AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outfit_prenda ADD CONSTRAINT FK_F8FE7B9865871137 FOREIGN KEY (prenda_id) REFERENCES prenda (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outfit_prenda DROP FOREIGN KEY FK_F8FE7B98AE96E385');
        $this->addSql('ALTER TABLE outfit_prenda DROP FOREIGN KEY FK_F8FE7B9865871137');
        $this->addSql('DROP TABLE outfit_prenda');
    }
}
