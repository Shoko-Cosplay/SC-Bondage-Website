<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210181423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "cosplayers_social_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "cosplayers_social" (id INT NOT NULL, cosplayer_id INT DEFAULT NULL, network VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFDE50C085A4C583 ON "cosplayers_social" (cosplayer_id)');
        $this->addSql('ALTER TABLE "cosplayers_social" ADD CONSTRAINT FK_DFDE50C085A4C583 FOREIGN KEY (cosplayer_id) REFERENCES "cosplayers" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cosplayers ADD description TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "cosplayers_social_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "cosplayers_social" DROP CONSTRAINT FK_DFDE50C085A4C583');
        $this->addSql('DROP TABLE "cosplayers_social"');
        $this->addSql('ALTER TABLE "cosplayers" DROP description');
    }
}
