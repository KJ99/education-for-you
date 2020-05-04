<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504002211 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE group_message_attachment (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_993ECD8C537A1329 (message_id), INDEX IDX_993ECD8C93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_message_attachment (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_75EC859537A1329 (message_id), INDEX IDX_75EC85993CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_message_attachment ADD CONSTRAINT FK_993ECD8C537A1329 FOREIGN KEY (message_id) REFERENCES group_message (id)');
        $this->addSql('ALTER TABLE group_message_attachment ADD CONSTRAINT FK_993ECD8C93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE system_message_attachment ADD CONSTRAINT FK_75EC859537A1329 FOREIGN KEY (message_id) REFERENCES system_message (id)');
        $this->addSql('ALTER TABLE system_message_attachment ADD CONSTRAINT FK_75EC85993CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE group_message_attachment');
        $this->addSql('DROP TABLE system_message_attachment');
    }
}
