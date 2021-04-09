<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409151142 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, banker_id INT NOT NULL, balance DOUBLE PRECISION NOT NULL, INDEX IDX_7D3656A438835980 (banker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banker (id INT AUTO_INCREMENT NOT NULL, validation_code VARCHAR(8) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE beneficiary (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, banker_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, iban VARCHAR(32) NOT NULL, bic VARCHAR(11) NOT NULL, INDEX IDX_7ABF446A9395C3F3 (customer_id), INDEX IDX_7ABF446A38835980 (banker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, birthday DATE NOT NULL, adress VARCHAR(255) NOT NULL, zip_code INT NOT NULL, city VARCHAR(50) NOT NULL, id_path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_81398E099B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, beneficiary_id INT NOT NULL, transfer_date DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_4034A3C09B6B5FBA (account_id), INDEX IDX_4034A3C0ECCAAFA0 (beneficiary_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A438835980 FOREIGN KEY (banker_id) REFERENCES banker (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446A38835980 FOREIGN KEY (banker_id) REFERENCES banker (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E099B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C09B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0ECCAAFA0 FOREIGN KEY (beneficiary_id) REFERENCES beneficiary (id)');
        $this->addSql('ALTER TABLE user ADD customer_id INT DEFAULT NULL, ADD banker_id INT DEFAULT NULL, ADD lastname VARCHAR(100) NOT NULL, ADD firstname VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64938835980 FOREIGN KEY (banker_id) REFERENCES banker (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6499395C3F3 ON user (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64938835980 ON user (banker_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E099B6B5FBA');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C09B6B5FBA');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A438835980');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446A38835980');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64938835980');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0ECCAAFA0');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446A9395C3F3');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499395C3F3');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE banker');
        $this->addSql('DROP TABLE beneficiary');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE transfer');
        $this->addSql('DROP INDEX UNIQ_8D93D6499395C3F3 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64938835980 ON user');
        $this->addSql('ALTER TABLE user DROP customer_id, DROP banker_id, DROP lastname, DROP firstname');
    }
}
