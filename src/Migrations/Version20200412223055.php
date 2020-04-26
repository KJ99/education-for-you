<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200412223055 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE completed_lessons (id INT AUTO_INCREMENT NOT NULL, lesson_id INT NOT NULL, user_id INT DEFAULT NULL, completion_date DATETIME NOT NULL, INDEX IDX_784EF3BDCDF80196 (lesson_id), INDEX IDX_784EF3BDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, mime VARCHAR(64) NOT NULL, directory VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8C9F3610D7DF1668 (file_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_download (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, user_id INT DEFAULT NULL, download_date DATETIME NOT NULL, INDEX IDX_C94A0DED93CB796C (file_id), INDEX IDX_C94A0DEDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, unit_id INT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, hidden TINYINT(1) NOT NULL, created DATETIME NOT NULL, remote_video VARCHAR(255) DEFAULT NULL, INDEX IDX_F87474F3F675F31B (author_id), INDEX IDX_F87474F3F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_attachment (id INT AUTO_INCREMENT NOT NULL, lesson_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_7456906ACDF80196 (lesson_id), INDEX IDX_7456906A93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_video (id INT AUTO_INCREMENT NOT NULL, lesson_id INT NOT NULL, file_id INT NOT NULL, UNIQUE INDEX UNIQ_36210EABCDF80196 (lesson_id), INDEX IDX_36210EAB93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_view (id INT AUTO_INCREMENT NOT NULL, lesson_id INT NOT NULL, user_id INT DEFAULT NULL, view_date DATETIME NOT NULL, INDEX IDX_C78C6DEACDF80196 (lesson_id), INDEX IDX_C78C6DEAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, name VARCHAR(255) NOT NULL, weight INT NOT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_9AEACC1323EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, reveiver_id INT NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, signature LONGTEXT DEFAULT NULL, sent_date DATETIME NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307F2B04EA88 (reveiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_attachment (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_B68FF524537A1329 (message_id), INDEX IDX_B68FF52493CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, mime VARCHAR(32) NOT NULL, public_directory VARCHAR(255) NOT NULL, directory VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_16DB4F89D7DF1668 (file_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, banner_id INT NOT NULL, coordinator_id INT NOT NULL, name VARCHAR(255) NOT NULL, hidden TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_FBCE3E7A5E237E06 (name), INDEX IDX_FBCE3E7A684EC833 (banner_id), INDEX IDX_FBCE3E7AE7877946 (coordinator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject_user (subject_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1F59529223EDC87 (subject_id), INDEX IDX_1F595292A76ED395 (user_id), PRIMARY KEY(subject_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, level_id INT NOT NULL, name VARCHAR(255) NOT NULL, weight INT NOT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_DCBB0C535FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tag VARCHAR(255) NOT NULL, expires DATETIME NOT NULL, content VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BDF55A63FEC530A9 (content), INDEX IDX_BDF55A63A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE completed_lessons ADD CONSTRAINT FK_784EF3BDCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE completed_lessons ADD CONSTRAINT FK_784EF3BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_download ADD CONSTRAINT FK_C94A0DED93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE file_download ADD CONSTRAINT FK_C94A0DEDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE lesson_attachment ADD CONSTRAINT FK_7456906ACDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson_attachment ADD CONSTRAINT FK_7456906A93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE lesson_video ADD CONSTRAINT FK_36210EABCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson_video ADD CONSTRAINT FK_36210EAB93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE lesson_view ADD CONSTRAINT FK_C78C6DEACDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson_view ADD CONSTRAINT FK_C78C6DEAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE level ADD CONSTRAINT FK_9AEACC1323EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2B04EA88 FOREIGN KEY (reveiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_attachment ADD CONSTRAINT FK_B68FF524537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message_attachment ADD CONSTRAINT FK_B68FF52493CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A684EC833 FOREIGN KEY (banner_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AE7877946 FOREIGN KEY (coordinator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subject_user ADD CONSTRAINT FK_1F59529223EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject_user ADD CONSTRAINT FK_1F595292A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C535FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE user_token ADD CONSTRAINT FK_BDF55A63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD avatar_id INT NOT NULL, ADD nickname VARCHAR(64) DEFAULT NULL, ADD first_name VARCHAR(64) DEFAULT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A188FE64 ON user (nickname)');
        $this->addSql('CREATE INDEX IDX_8D93D64986383B10 ON user (avatar_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE file_download DROP FOREIGN KEY FK_C94A0DED93CB796C');
        $this->addSql('ALTER TABLE lesson_attachment DROP FOREIGN KEY FK_7456906A93CB796C');
        $this->addSql('ALTER TABLE lesson_video DROP FOREIGN KEY FK_36210EAB93CB796C');
        $this->addSql('ALTER TABLE message_attachment DROP FOREIGN KEY FK_B68FF52493CB796C');
        $this->addSql('ALTER TABLE completed_lessons DROP FOREIGN KEY FK_784EF3BDCDF80196');
        $this->addSql('ALTER TABLE lesson_attachment DROP FOREIGN KEY FK_7456906ACDF80196');
        $this->addSql('ALTER TABLE lesson_video DROP FOREIGN KEY FK_36210EABCDF80196');
        $this->addSql('ALTER TABLE lesson_view DROP FOREIGN KEY FK_C78C6DEACDF80196');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C535FB14BA7');
        $this->addSql('ALTER TABLE message_attachment DROP FOREIGN KEY FK_B68FF524537A1329');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A684EC833');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('ALTER TABLE level DROP FOREIGN KEY FK_9AEACC1323EDC87');
        $this->addSql('ALTER TABLE subject_user DROP FOREIGN KEY FK_1F59529223EDC87');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3F8BD700D');
        $this->addSql('DROP TABLE completed_lessons');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_download');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_attachment');
        $this->addSql('DROP TABLE lesson_video');
        $this->addSql('DROP TABLE lesson_view');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_attachment');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE subject_user');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE user_token');
        $this->addSql('DROP INDEX UNIQ_8D93D649A188FE64 ON user');
        $this->addSql('DROP INDEX IDX_8D93D64986383B10 ON user');
        $this->addSql('ALTER TABLE user DROP avatar_id, DROP nickname, DROP first_name, DROP active');
    }
}
