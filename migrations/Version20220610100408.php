<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610100408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD last_confirm_code_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activate_code CHANGE code code VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE code_type code_type VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE sent_by sent_by VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE file CHANGE path path VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE locale_name CHANGE name_ru name_ru LONGTEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE name_en name_en LONGTEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE name_ua name_ua LONGTEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE name_de name_de LONGTEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE news CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE text text LONGTEXT NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE oauth2_access_token CHANGE identifier identifier CHAR(80) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE client client VARCHAR(32) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE user_identifier user_identifier VARCHAR(128) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE scopes scopes TEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:oauth2_scope)\'');
        $this->addSql('ALTER TABLE oauth2_authorization_code CHANGE identifier identifier CHAR(80) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE client client VARCHAR(32) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE user_identifier user_identifier VARCHAR(128) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE scopes scopes TEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:oauth2_scope)\'');
        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(32) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE name name VARCHAR(128) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE secret secret VARCHAR(128) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE redirect_uris redirect_uris TEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:oauth2_redirect_uri)\', CHANGE grants grants TEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:oauth2_grant)\', CHANGE scopes scopes TEXT DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:oauth2_scope)\'');
        $this->addSql('ALTER TABLE oauth2_refresh_token CHANGE identifier identifier CHAR(80) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE access_token access_token CHAR(80) DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE sms CHANGE phone phone VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE message message VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP last_confirm_code_date, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE middle_name middle_name VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE email_canonical email_canonical VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`, CHANGE username_canonical username_canonical VARCHAR(255) NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
