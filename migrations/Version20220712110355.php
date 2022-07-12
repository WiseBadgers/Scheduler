<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712110355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note_type CHANGE name name VARCHAR(30) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CA446715E237E06 ON note_type (name)');
        $this->addSql('ALTER TABLE school_class CHANGE name name VARCHAR(2) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33B1AF855E237E06 ON school_class (name)');
        $this->addSql('ALTER TABLE semester CHANGE name name VARCHAR(30) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7388EED5E237E06 ON semester (name)');
        $this->addSql('ALTER TABLE subject CHANGE name name VARCHAR(30) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBCE3E7A5E237E06 ON subject (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2CA446715E237E06 ON note_type');
        $this->addSql('ALTER TABLE note_type CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_33B1AF855E237E06 ON school_class');
        $this->addSql('ALTER TABLE school_class CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_F7388EED5E237E06 ON semester');
        $this->addSql('ALTER TABLE semester CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_FBCE3E7A5E237E06 ON subject');
        $this->addSql('ALTER TABLE subject CHANGE name name VARCHAR(255) NOT NULL');
    }
}
