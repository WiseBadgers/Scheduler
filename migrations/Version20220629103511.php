<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220629103511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB926ED0855');
        $this->addSql('DROP INDEX IDX_169E6FB926ED0855 ON course');
        $this->addSql('ALTER TABLE course CHANGE note_id school_class_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB914463F54 FOREIGN KEY (school_class_id) REFERENCES school_class (id)');
        $this->addSql('CREATE INDEX IDX_169E6FB914463F54 ON course (school_class_id)');
        $this->addSql('ALTER TABLE note ADD course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14591CC992 ON note (course_id)');
        $this->addSql('ALTER TABLE user ADD school_class_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64914463F54 FOREIGN KEY (school_class_id) REFERENCES school_class (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64914463F54 ON user (school_class_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB914463F54');
        $this->addSql('DROP INDEX IDX_169E6FB914463F54 ON course');
        $this->addSql('ALTER TABLE course CHANGE school_class_id note_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB926ED0855 FOREIGN KEY (note_id) REFERENCES note (id)');
        $this->addSql('CREATE INDEX IDX_169E6FB926ED0855 ON course (note_id)');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14591CC992');
        $this->addSql('DROP INDEX IDX_CFBDFA14591CC992 ON note');
        $this->addSql('ALTER TABLE note DROP course_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64914463F54');
        $this->addSql('DROP INDEX IDX_8D93D64914463F54 ON user');
        $this->addSql('ALTER TABLE user DROP school_class_id');
    }
}
