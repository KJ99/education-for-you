<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200412223415 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE student_group (id INT AUTO_INCREMENT NOT NULL, teacher_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E5F73D5841807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_group_user (student_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_491660D84DDF95DC (student_group_id), INDEX IDX_491660D8A76ED395 (user_id), PRIMARY KEY(student_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_group ADD CONSTRAINT FK_E5F73D5841807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_group_user ADD CONSTRAINT FK_491660D84DDF95DC FOREIGN KEY (student_group_id) REFERENCES student_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_group_user ADD CONSTRAINT FK_491660D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE student_group_user DROP FOREIGN KEY FK_491660D84DDF95DC');
        $this->addSql('DROP TABLE student_group');
        $this->addSql('DROP TABLE student_group_user');
    }
}
