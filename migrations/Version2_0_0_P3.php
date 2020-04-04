<?php

declare(strict_types=1);

namespace AppBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version2_0_0_P3 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Ajout des sessions en base de données';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sessions (sess_id VARCHAR(128) NOT NULL PRIMARY KEY, sess_data BLOB NOT NULL, sess_time INTEGER UNSIGNED NOT NULL, sess_lifetime INTEGER UNSIGNED NOT NULL) COLLATE utf8mb4_bin, ENGINE = InnoDB;');
    }

    public function down(Schema $schema) : void
    {

    }
}
