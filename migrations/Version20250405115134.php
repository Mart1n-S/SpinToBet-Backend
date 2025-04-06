<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405115134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE referral_link (id INT AUTO_INCREMENT NOT NULL, referrer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', referred_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_25FEEC23798C22DB (referrer_id), UNIQUE INDEX UNIQ_25FEEC23CFE2A98 (referred_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referral_link ADD CONSTRAINT FK_25FEEC23798C22DB FOREIGN KEY (referrer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE referral_link ADD CONSTRAINT FK_25FEEC23CFE2A98 FOREIGN KEY (referred_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD referral_code VARCHAR(32) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496447454A ON user (referral_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE referral_link DROP FOREIGN KEY FK_25FEEC23798C22DB');
        $this->addSql('ALTER TABLE referral_link DROP FOREIGN KEY FK_25FEEC23CFE2A98');
        $this->addSql('DROP TABLE referral_link');
        $this->addSql('DROP INDEX UNIQ_8D93D6496447454A ON user');
        $this->addSql('ALTER TABLE user DROP referral_code');
    }
}
