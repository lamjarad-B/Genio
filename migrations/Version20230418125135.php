<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418125135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, date_naissance DATE NOT NULL, date_deces DATE DEFAULT NULL, sexe VARCHAR(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relations (id INT AUTO_INCREMENT NOT NULL, personne_une_id INT NOT NULL, relation_type_id INT DEFAULT NULL, personne_deux_id INT NOT NULL, INDEX IDX_146CBF786DADD703 (personne_une_id), INDEX IDX_146CBF78DC379EE2 (relation_type_id), INDEX IDX_146CBF78629A3672 (personne_deux_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_relation (id INT AUTO_INCREMENT NOT NULL, nom_relation VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE relations ADD CONSTRAINT FK_146CBF786DADD703 FOREIGN KEY (personne_une_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE relations ADD CONSTRAINT FK_146CBF78DC379EE2 FOREIGN KEY (relation_type_id) REFERENCES type_relation (id)');
        $this->addSql('ALTER TABLE relations ADD CONSTRAINT FK_146CBF78629A3672 FOREIGN KEY (personne_deux_id) REFERENCES personne (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relations DROP FOREIGN KEY FK_146CBF786DADD703');
        $this->addSql('ALTER TABLE relations DROP FOREIGN KEY FK_146CBF78DC379EE2');
        $this->addSql('ALTER TABLE relations DROP FOREIGN KEY FK_146CBF78629A3672');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE relations');
        $this->addSql('DROP TABLE type_relation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
