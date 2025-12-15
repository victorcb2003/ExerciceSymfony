<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215142423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products ADD name VARCHAR(128) NOT NULL, ADD userid VARCHAR(128) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD size INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON products');
        $this->addSql('ALTER TABLE products DROP name, DROP userid, DROP description, DROP size, CHANGE id id INT DEFAULT NULL');
    }
}
