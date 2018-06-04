<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180604081131 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, recurrence VARCHAR(64) NOT NULL, INDEX IDX_E3F1A9A19EB6921 (client_id), INDEX IDX_E3F1A9AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_E3F1A9A19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_E3F1A9AA76ED395 FOREIGN KEY (user_id) REFERENCES ldap_user (id)');
        $this->addSql('ALTER TABLE backup ADD user_id INT DEFAULT NULL, ADD type VARCHAR(64) NOT NULL');
        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_3FF0D1ACA76ED395 FOREIGN KEY (user_id) REFERENCES ldap_user (id)');
        $this->addSql('CREATE INDEX IDX_3FF0D1ACA76ED395 ON backup (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE schedule');
        $this->addSql('ALTER TABLE backup DROP FOREIGN KEY FK_3FF0D1ACA76ED395');
        $this->addSql('DROP INDEX IDX_3FF0D1ACA76ED395 ON backup');
        $this->addSql('ALTER TABLE backup DROP user_id, DROP type');
    }
}
