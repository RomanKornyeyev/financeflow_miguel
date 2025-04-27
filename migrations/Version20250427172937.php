<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427172937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE movimiento (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, tipo_transaccion VARCHAR(255) NOT NULL, categoria VARCHAR(255) NOT NULL, importe NUMERIC(10, 2) NOT NULL, concepto VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', fecha_movimiento DATE NOT NULL, INDEX IDX_C8FF107ADB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento ADD CONSTRAINT FK_C8FF107ADB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento DROP FOREIGN KEY FK_C8FF107ADB38439E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE movimiento
        SQL);
    }
}
