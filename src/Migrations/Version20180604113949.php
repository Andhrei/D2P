<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180604113949 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB19EB6921');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB94A4C7D4');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBA76ED395');
        $this->addSql('DROP INDEX IDX_5A3811FB19EB6921 ON schedule');
        $this->addSql('DROP INDEX IDX_5A3811FBA76ED395 ON schedule');
        $this->addSql('DROP INDEX IDX_5A3811FB94A4C7D4 ON schedule');
        $this->addSql('ALTER TABLE schedule DROP client_id, DROP user_id, DROP device_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule ADD client_id INT NOT NULL, ADD user_id INT NOT NULL, ADD device_id INT NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBA76ED395 FOREIGN KEY (user_id) REFERENCES ldap_user (id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB19EB6921 ON schedule (client_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FBA76ED395 ON schedule (user_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB94A4C7D4 ON schedule (device_id)');
    }
}
