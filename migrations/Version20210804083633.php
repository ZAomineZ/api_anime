<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804083633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE anime ADD type_anime_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE anime ADD CONSTRAINT FK_13045942B4B49975 FOREIGN KEY (type_anime_id) REFERENCES type_anime (id)');
        $this->addSql('CREATE INDEX IDX_13045942B4B49975 ON anime (type_anime_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE anime DROP FOREIGN KEY FK_13045942B4B49975');
        $this->addSql('DROP INDEX IDX_13045942B4B49975 ON anime');
        $this->addSql('ALTER TABLE anime DROP type_anime_id');
    }
}
