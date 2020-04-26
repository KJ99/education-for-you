<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200412224325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE group_join_request (id INT AUTO_INCREMENT NOT NULL, student_group_id INT NOT NULL, user_id INT NOT NULL, accepted TINYINT(1) DEFAULT NULL, INDEX IDX_574AEC64DDF95DC (student_group_id), INDEX IDX_574AEC6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_message (id INT AUTO_INCREMENT NOT NULL, student_group_id INT NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, sent_date DATETIME NOT NULL, INDEX IDX_30BD64734DDF95DC (student_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_message_view (id INT AUTO_INCREMENT NOT NULL, student_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BB2E7A6E4DDF95DC (student_group_id), INDEX IDX_BB2E7A6EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE live_lesson (id INT AUTO_INCREMENT NOT NULL, student_group_id INT NOT NULL, title VARCHAR(255) NOT NULL, start DATETIME NOT NULL, meetup_url VARCHAR(255) DEFAULT NULL, INDEX IDX_9B22C2274DDF95DC (student_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_join_request ADD CONSTRAINT FK_574AEC64DDF95DC FOREIGN KEY (student_group_id) REFERENCES student_group (id)');
        $this->addSql('ALTER TABLE group_join_request ADD CONSTRAINT FK_574AEC6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE group_message ADD CONSTRAINT FK_30BD64734DDF95DC FOREIGN KEY (student_group_id) REFERENCES student_group (id)');
        $this->addSql('ALTER TABLE group_message_view ADD CONSTRAINT FK_BB2E7A6E4DDF95DC FOREIGN KEY (student_group_id) REFERENCES student_group (id)');
        $this->addSql('ALTER TABLE group_message_view ADD CONSTRAINT FK_BB2E7A6EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE live_lesson ADD CONSTRAINT FK_9B22C2274DDF95DC FOREIGN KEY (student_group_id) REFERENCES student_group (id)');
        $this->addSql('ALTER TABLE student_group ADD level_id INT NOT NULL, ADD avatar_id INT NOT NULL, ADD color VARCHAR(7) NOT NULL, ADD auto_accept TINYINT(1) NOT NULL, ADD hidden TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE student_group ADD CONSTRAINT FK_E5F73D585FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE student_group ADD CONSTRAINT FK_E5F73D5886383B10 FOREIGN KEY (avatar_id) REFERENCES picture (id)');
        $this->addSql('CREATE INDEX IDX_E5F73D585FB14BA7 ON student_group (level_id)');
        $this->addSql('CREATE INDEX IDX_E5F73D5886383B10 ON student_group (avatar_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE group_join_request');
        $this->addSql('DROP TABLE group_message');
        $this->addSql('DROP TABLE group_message_view');
        $this->addSql('DROP TABLE live_lesson');
        $this->addSql('ALTER TABLE student_group DROP FOREIGN KEY FK_E5F73D585FB14BA7');
        $this->addSql('ALTER TABLE student_group DROP FOREIGN KEY FK_E5F73D5886383B10');
        $this->addSql('DROP INDEX IDX_E5F73D585FB14BA7 ON student_group');
        $this->addSql('DROP INDEX IDX_E5F73D5886383B10 ON student_group');
        $this->addSql('ALTER TABLE student_group DROP level_id, DROP avatar_id, DROP color, DROP auto_accept, DROP hidden');
    }
}
