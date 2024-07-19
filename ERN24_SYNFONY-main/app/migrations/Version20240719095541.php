<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719095541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facturation ADD invoice_number VARCHAR(20) NOT NULL, ADD invoice_date DATE NOT NULL, ADD due_date DATE NOT NULL, ADD total_amount NUMERIC(10, 2) NOT NULL, ADD tax_amount NUMERIC(10, 2) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE facturation ADD CONSTRAINT FK_17EB513A19EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17EB513A2DA68207 ON facturation (invoice_number)');
        $this->addSql('CREATE INDEX IDX_17EB513A19EB6921 ON facturation (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facturation DROP FOREIGN KEY FK_17EB513A19EB6921');
        $this->addSql('DROP INDEX UNIQ_17EB513A2DA68207 ON facturation');
        $this->addSql('DROP INDEX IDX_17EB513A19EB6921 ON facturation');
        $this->addSql('ALTER TABLE facturation DROP invoice_number, DROP invoice_date, DROP due_date, DROP total_amount, DROP tax_amount, DROP created_at, DROP updated_at, DROP client_id');
    }
}
