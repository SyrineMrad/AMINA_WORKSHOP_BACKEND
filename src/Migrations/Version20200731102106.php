<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200731102106 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE papier ADD user_id_id INT DEFAULT NULL, ADD n VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE papier ADD CONSTRAINT FK_940A2D5E9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_940A2D5E9D86650F ON papier (user_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE papier DROP FOREIGN KEY FK_940A2D5E9D86650F');
        $this->addSql('DROP INDEX IDX_940A2D5E9D86650F ON papier');
        $this->addSql('ALTER TABLE papier DROP user_id_id, DROP n');
    }
}
