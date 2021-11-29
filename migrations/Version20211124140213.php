<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124140213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notion_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, notion_id VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE send_email (id INT AUTO_INCREMENT NOT NULL, uuser_id INT NOT NULL, notion_page_id INT NOT NULL, INDEX IDX_8EF07932BB904C76 (uuser_id), INDEX IDX_8EF079321ACAE608 (notion_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE send_email ADD CONSTRAINT FK_8EF07932BB904C76 FOREIGN KEY (uuser_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE send_email ADD CONSTRAINT FK_8EF079321ACAE608 FOREIGN KEY (notion_page_id) REFERENCES notion_page (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE send_email DROP FOREIGN KEY FK_8EF079321ACAE608');
        $this->addSql('ALTER TABLE send_email DROP FOREIGN KEY FK_8EF07932BB904C76');
        $this->addSql('DROP TABLE notion_page');
        $this->addSql('DROP TABLE send_email');
        $this->addSql('DROP TABLE user');
    }
}
