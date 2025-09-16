<?php

declare(strict_types=1);

namespace backupmigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914200953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A301EC62');
        $this->addSql('DROP INDEX IDX_5F9E962A301EC62 ON comments');
        $this->addSql('ALTER TABLE comments ADD photo_id INT NOT NULL, DROP photos_id');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A7E9E4C8C ON comments (photo_id)');
        $this->addSql('ALTER TABLE photos RENAME INDEX idx_fdae5ef4e7af8f TO IDX_876E0D94E7AF8F');
        $this->addSql('ALTER TABLE users CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE birth_year birth_year DATE DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A7E9E4C8C');
        $this->addSql('DROP INDEX IDX_5F9E962A7E9E4C8C ON comments');
        $this->addSql('ALTER TABLE comments ADD photos_id INT DEFAULT NULL, DROP photo_id');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A301EC62 FOREIGN KEY (photos_id) REFERENCES photos (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A301EC62 ON comments (photos_id)');
        $this->addSql('ALTER TABLE photos RENAME INDEX idx_876e0d94e7af8f TO IDX_FDAE5EF4E7AF8F');
        $this->addSql('ALTER TABLE users CHANGE first_name first_name VARCHAR(255) DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE birth_year birth_year DATE DEFAULT \'NULL\', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
