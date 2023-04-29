<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403131803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492391A6B2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493124095C');
        $this->addSql('DROP INDEX IDX_8D93D6492391A6B2 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6493124095C ON user');
        $this->addSql('ALTER TABLE user DROP id_parent1_id, DROP id_parent2_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD id_parent1_id INT DEFAULT NULL, ADD id_parent2_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492391A6B2 FOREIGN KEY (id_parent1_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493124095C FOREIGN KEY (id_parent2_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D6492391A6B2 ON user (id_parent1_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6493124095C ON user (id_parent2_id)');
    }
}