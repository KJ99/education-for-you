<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200508162949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE privacy_policy ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE privacy_policy ADD CONSTRAINT FK_3EE6A81BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3EE6A81BF675F31B ON privacy_policy (author_id)');
        $this->addSql('ALTER TABLE site_description ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE site_description ADD CONSTRAINT FK_86CA7F96F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_86CA7F96F675F31B ON site_description (author_id)');
        $this->addSql('ALTER TABLE terms_of_use ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE terms_of_use ADD CONSTRAINT FK_C2864F2AF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C2864F2AF675F31B ON terms_of_use (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE privacy_policy DROP FOREIGN KEY FK_3EE6A81BF675F31B');
        $this->addSql('DROP INDEX IDX_3EE6A81BF675F31B ON privacy_policy');
        $this->addSql('ALTER TABLE privacy_policy DROP author_id');
        $this->addSql('ALTER TABLE site_description DROP FOREIGN KEY FK_86CA7F96F675F31B');
        $this->addSql('DROP INDEX IDX_86CA7F96F675F31B ON site_description');
        $this->addSql('ALTER TABLE site_description DROP author_id');
        $this->addSql('ALTER TABLE terms_of_use DROP FOREIGN KEY FK_C2864F2AF675F31B');
        $this->addSql('DROP INDEX IDX_C2864F2AF675F31B ON terms_of_use');
        $this->addSql('ALTER TABLE terms_of_use DROP author_id');
    }
}
