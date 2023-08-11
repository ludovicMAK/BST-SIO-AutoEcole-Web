<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109145630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, sexe SMALLINT NOT NULL, date_de_naissance DATE NOT NULL, adresse VARCHAR(75) NOT NULL, code_postal VARCHAR(5) NOT NULL, ville VARCHAR(50) NOT NULL, telephone VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_ECA105F7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lecon (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, moniteur_id INT NOT NULL, vehicule_id INT NOT NULL, date DATE NOT NULL, heure VARCHAR(10) NOT NULL, payee TINYINT(1) NOT NULL, INDEX IDX_94E6242EA6CC7B2 (eleve_id), INDEX IDX_94E6242EA234A5D3 (moniteur_id), INDEX IDX_94E6242E4A4A3511 (vehicule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE licence (id INT AUTO_INCREMENT NOT NULL, moniteur_id INT NOT NULL, categorie_id INT NOT NULL, date_obtention DATE NOT NULL, INDEX IDX_1DAAE648A234A5D3 (moniteur_id), INDEX IDX_1DAAE648BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moniteur (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, sexe SMALLINT NOT NULL, date_de_naissance DATE NOT NULL, adresse VARCHAR(75) NOT NULL, code_postal VARCHAR(5) NOT NULL, ville VARCHAR(50) NOT NULL, telephone VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_B3EC8EBAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, role SMALLINT NOT NULL, UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, immatriculation VARCHAR(10) NOT NULL, marque VARCHAR(30) NOT NULL, modele VARCHAR(30) NOT NULL, annee VARCHAR(4) NOT NULL, INDEX IDX_292FFF1DBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lecon ADD CONSTRAINT FK_94E6242EA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE lecon ADD CONSTRAINT FK_94E6242EA234A5D3 FOREIGN KEY (moniteur_id) REFERENCES moniteur (id)');
        $this->addSql('ALTER TABLE lecon ADD CONSTRAINT FK_94E6242E4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE licence ADD CONSTRAINT FK_1DAAE648A234A5D3 FOREIGN KEY (moniteur_id) REFERENCES moniteur (id)');
        $this->addSql('ALTER TABLE licence ADD CONSTRAINT FK_1DAAE648BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE moniteur ADD CONSTRAINT FK_B3EC8EBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7A76ED395');
        $this->addSql('ALTER TABLE lecon DROP FOREIGN KEY FK_94E6242EA6CC7B2');
        $this->addSql('ALTER TABLE lecon DROP FOREIGN KEY FK_94E6242EA234A5D3');
        $this->addSql('ALTER TABLE lecon DROP FOREIGN KEY FK_94E6242E4A4A3511');
        $this->addSql('ALTER TABLE licence DROP FOREIGN KEY FK_1DAAE648A234A5D3');
        $this->addSql('ALTER TABLE licence DROP FOREIGN KEY FK_1DAAE648BCF5E72D');
        $this->addSql('ALTER TABLE moniteur DROP FOREIGN KEY FK_B3EC8EBAA76ED395');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DBCF5E72D');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE lecon');
        $this->addSql('DROP TABLE licence');
        $this->addSql('DROP TABLE moniteur');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
