<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414141635 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Beneficiary (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, banker_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, iban VARCHAR(32) NOT NULL, bic VARCHAR(11) NOT NULL, is_validated TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_FC23CBBD9395C3F3 (customer_id), INDEX IDX_FC23CBBD38835980 (banker_id), UNIQUE INDEX UNIQ_FC23CBBDCC4083D69395C3F3 (IBAN, customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, banker_id INT NOT NULL, balance DOUBLE PRECISION NOT NULL, bank_account_id VARCHAR(11) DEFAULT NULL, INDEX IDX_7D3656A438835980 (banker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banker (id INT AUTO_INCREMENT NOT NULL, validation_code VARCHAR(8) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, birthday DATE NOT NULL, adress VARCHAR(255) NOT NULL, zip_code INT NOT NULL, city VARCHAR(50) NOT NULL, id_path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_81398E099B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, beneficiary_id INT NOT NULL, transfer_date DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_4034A3C09B6B5FBA (account_id), INDEX IDX_4034A3C0ECCAAFA0 (beneficiary_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, banker_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, lastname VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6499395C3F3 (customer_id), UNIQUE INDEX UNIQ_8D93D64938835980 (banker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Beneficiary ADD CONSTRAINT FK_FC23CBBD9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE Beneficiary ADD CONSTRAINT FK_FC23CBBD38835980 FOREIGN KEY (banker_id) REFERENCES banker (id)');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A438835980 FOREIGN KEY (banker_id) REFERENCES banker (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E099B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C09B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0ECCAAFA0 FOREIGN KEY (beneficiary_id) REFERENCES Beneficiary (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64938835980 FOREIGN KEY (banker_id) REFERENCES banker (id)');
        // add banker account
        $this->addSql("INSERT INTO banker VALUES (1,'12345678'), (2,'12345678'), (3,'12345678'), (4,'12345678'), (5,'12345678');");
        $this->addSql("INSERT INTO user VALUES  (1, NULL, 1, 'banker1@golgoth.fr', '[\"ROLE_BANKER\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$QzVPOGJENGpUSkFMeFc4Vw$4Genhmlu56ed9KKzQPh/x2ps13antENmhxyT7Y6lJ8I', 'banquier', 'Alain');");
        $this->addSql("INSERT INTO user VALUES  (2, NULL, 2, 'banker2@golgoth.fr', '[\"ROLE_BANKER\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$QzVPOGJENGpUSkFMeFc4Vw$4Genhmlu56ed9KKzQPh/x2ps13antENmhxyT7Y6lJ8I', 'banquier', 'Benoit');");
        $this->addSql("INSERT INTO user VALUES  (3, NULL, 3, 'banker3@golgoth.fr', '[\"ROLE_BANKER\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$QzVPOGJENGpUSkFMeFc4Vw$4Genhmlu56ed9KKzQPh/x2ps13antENmhxyT7Y6lJ8I', 'banquier', 'Charlie');");
        $this->addSql("INSERT INTO user VALUES  (4, NULL, 4, 'banker4@golgoth.fr', '[\"ROLE_BANKER\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$QzVPOGJENGpUSkFMeFc4Vw$4Genhmlu56ed9KKzQPh/x2ps13antENmhxyT7Y6lJ8I', 'banquier', 'Denis');");
        $this->addSql("INSERT INTO user VALUES  (5, NULL, 5, 'banker5@golgoth.fr', '[\"ROLE_BANKER\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$QzVPOGJENGpUSkFMeFc4Vw$4Genhmlu56ed9KKzQPh/x2ps13antENmhxyT7Y6lJ8I', 'banquier', 'Emile');");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0ECCAAFA0');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E099B6B5FBA');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C09B6B5FBA');
        $this->addSql('ALTER TABLE Beneficiary DROP FOREIGN KEY FK_FC23CBBD38835980');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A438835980');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64938835980');
        $this->addSql('ALTER TABLE Beneficiary DROP FOREIGN KEY FK_FC23CBBD9395C3F3');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499395C3F3');
        $this->addSql('DROP TABLE Beneficiary');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE banker');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE transfer');
        $this->addSql('DROP TABLE user');
    }
}
