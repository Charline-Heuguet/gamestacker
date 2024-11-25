<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108134015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report_comment ADD comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report_comment ADD CONSTRAINT FK_F4ED2F6CF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_F4ED2F6CF8697D13 ON report_comment (comment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report_comment DROP FOREIGN KEY FK_F4ED2F6CF8697D13');
        $this->addSql('DROP INDEX IDX_F4ED2F6CF8697D13 ON report_comment');
        $this->addSql('ALTER TABLE report_comment DROP comment_id');
    }
}
