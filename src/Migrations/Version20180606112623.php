<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180606112623 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE folder ADD folder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id)');
        $this->addSql('CREATE INDEX IDX_ECA209CD162CB942 ON folder (folder_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE folder DROP FOREIGN KEY FK_ECA209CD162CB942');
        $this->addSql('DROP INDEX IDX_ECA209CD162CB942 ON folder');
        $this->addSql('ALTER TABLE folder DROP folder_id');
    }
}
