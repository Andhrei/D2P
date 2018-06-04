<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180604174437 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACA76ED395');
        $this->addSql('DROP INDEX IDX_3FF0D1ACA76ED395 ON backup');
        $this->addSql('ALTER TABLE backup DROP user_id, DROP type');
        $this->addSql('ALTER TABLE datalist ADD type VARCHAR(64) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE backup ADD user_id INT DEFAULT NULL, ADD type VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACA76ED395 FOREIGN KEY (user_id) REFERENCES ldap_user (id)');
        $this->addSql('CREATE INDEX IDX_3FF0D1ACA76ED395 ON backup (user_id)');
        $this->addSql('ALTER TABLE datalist DROP type');
    }
}
