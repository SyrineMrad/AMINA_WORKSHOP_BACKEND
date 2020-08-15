<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200815135738 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D704DEB9');
        $this->addSql('DROP INDEX IDX_8F91ABF0D704DEB9 ON avis');
        $this->addSql('ALTER TABLE avis CHANGE papiers_id papier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0C09C75EF FOREIGN KEY (papier_id) REFERENCES papier (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0C09C75EF ON avis (papier_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0C09C75EF');
        $this->addSql('DROP INDEX IDX_8F91ABF0C09C75EF ON avis');
        $this->addSql('ALTER TABLE avis CHANGE papier_id papiers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D704DEB9 FOREIGN KEY (papiers_id) REFERENCES papier (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0D704DEB9 ON avis (papiers_id)');
    }
}
