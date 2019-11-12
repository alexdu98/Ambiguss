<?php

declare(strict_types=1);

namespace AppBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version2_0_0_P1 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE glose DROP FOREIGN KEY FK_E681270460BB6FE6');
        $this->addSql('ALTER TABLE glose DROP FOREIGN KEY FK_E6812704D3DF658');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C2122EE1947');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC6A99F74A');
        $this->addSql('ALTER TABLE jugement DROP FOREIGN KEY FK_F53C8E501391DFBF');
        $this->addSql('ALTER TABLE jugement DROP FOREIGN KEY FK_F53C8E5038DFE8A6');
        $this->addSql('ALTER TABLE jugement DROP FOREIGN KEY FK_F53C8E5057AFE0E9');
        $this->addSql('ALTER TABLE jugement DROP FOREIGN KEY FK_F53C8E505F6C043E');
        $this->addSql('ALTER TABLE jugement DROP FOREIGN KEY FK_F53C8E5060BB6FE6');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29B3E9C81');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB297A45358C');
        $this->addSql('ALTER TABLE mot_ambigu DROP FOREIGN KEY FK_C73E770060BB6FE6');
        $this->addSql('ALTER TABLE mot_ambigu DROP FOREIGN KEY FK_C73E7700D3DF658');
        $this->addSql('ALTER TABLE mot_ambigu_phrase DROP FOREIGN KEY FK_B8D4ECAE8671F084');
        $this->addSql('ALTER TABLE mot_ambigu_phrase DROP FOREIGN KEY FK_B8D4ECAEC75E376D');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D8671F084');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DA9E2D76C');
        $this->addSql('ALTER TABLE phrase DROP FOREIGN KEY FK_A24BE60C60BB6FE6');
        $this->addSql('ALTER TABLE phrase DROP FOREIGN KEY FK_A24BE60CD3DF658');
        $this->addSql('ALTER TABLE niveau DROP FOREIGN KEY FK_4BDFF36B20AB2E86');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7B3E9C81');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC75DAA1781');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC721326CD9');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC760BB6FE6');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7C6D21FCF');
        $this->addSql('ALTER TABLE succes DROP FOREIGN KEY FK_BFC223836716180A');
        $this->addSql('ALTER TABLE succes DROP FOREIGN KEY FK_BFC22383FA37F5BB');
        $this->addSql('ALTER TABLE succes_membre DROP FOREIGN KEY FK_E1857E2A4EF1B4AB');

        $this->addSql('DROP INDEX UNIQ_CDBBCF3ACDBBCF3A ON categorie_jugement');
        $this->addSql('DROP INDEX UNIQ_E68127041B44CD51 ON glose');
        $this->addSql('DROP INDEX IDX_E681270460BB6FE6 ON glose');
        $this->addSql('DROP INDEX IDX_E6812704D3DF658 ON glose');
        $this->addSql('DROP INDEX IDX_GLOSE_DATECREATION ON glose');
        $this->addSql('DROP INDEX IDX_GLOSE_DATEMODIFICATION ON glose');
        $this->addSql('DROP INDEX IDX_4B98C2122EE1947 ON groupe');
        $this->addSql('DROP INDEX UNIQ_4B98C216C6E55B5 ON groupe');
        $this->addSql('DROP INDEX IDX_EDBFD5EC6A99F74A ON historique');
        $this->addSql('DROP INDEX IDX_HISTORIQUE_DATEACTION ON historique');
        $this->addSql('DROP INDEX IDX_F53C8E501391DFBF ON jugement');
        $this->addSql('DROP INDEX IDX_F53C8E5038DFE8A6 ON jugement');
        $this->addSql('DROP INDEX IDX_F53C8E5057AFE0E9 ON jugement');
        $this->addSql('DROP INDEX IDX_F53C8E505F6C043E ON jugement');
        $this->addSql('DROP INDEX IDX_F53C8E5060BB6FE6 ON jugement');
        $this->addSql('DROP INDEX IDX_JUGEMENT_DATECREATION ON jugement');
        $this->addSql('DROP INDEX IDX_JUGEMENT_DATEDELIBERATION ON jugement');
        $this->addSql('DROP INDEX IDX_JUGEMENT_IDOBJET ON jugement');
        $this->addSql('DROP INDEX UNIQ_F6B4FB2986CC499D ON membre');
        $this->addSql('DROP INDEX UNIQ_F6B4FB293BAF2475 ON membre');
        $this->addSql('DROP INDEX IDX_F6B4FB297A45358C ON membre');
        $this->addSql('DROP INDEX IDX_MEMBRE_DATE_CONNEXION ON membre');
        $this->addSql('DROP INDEX UNIQ_F6B4FB29E7927C74 ON membre');
        $this->addSql('DROP INDEX UNIQ_F6B4FB29EA2BF86C ON membre');
        $this->addSql('DROP INDEX UNIQ_F6B4FB29C8F7D2C6 ON membre');
        $this->addSql('DROP INDEX IDX_F6B4FB29B3E9C81 ON membre');
        $this->addSql('DROP INDEX IDX_MEMBRE_CREDITS ON membre');
        $this->addSql('DROP INDEX IDX_MEMBRE_DATEINSCRIPTION ON membre');
        $this->addSql('DROP INDEX IDX_MEMBRE_DATENAISSANCE ON membre');
        $this->addSql('DROP INDEX IDX_MEMBRE_POINTSCLASSEMENT ON membre');
        $this->addSql('DROP INDEX IDX_MEMBRE_SEXE ON membre');
        $this->addSql('DROP INDEX IDX_C73E770060BB6FE6 ON mot_ambigu');
        $this->addSql('DROP INDEX IDX_C73E7700D3DF658 ON mot_ambigu');
        $this->addSql('DROP INDEX IDX_MOTAMBIGU_DATECREATION ON mot_ambigu');
        $this->addSql('DROP INDEX IDX_MOTAMBIGU_DATEMODIFICATION ON mot_ambigu');
        $this->addSql('DROP INDEX UNIQ_C73E77001B44CD51 ON mot_ambigu');
        $this->addSql('DROP INDEX IDX_B8D4ECAE8671F084 ON mot_ambigu_phrase');
        $this->addSql('DROP INDEX IDX_B8D4ECAEC75E376D ON mot_ambigu_phrase');
        $this->addSql('DROP INDEX IDX_59B1F3D8671F084 ON partie');
        $this->addSql('DROP INDEX IDX_59B1F3DA9E2D76C ON partie');
        $this->addSql('DROP INDEX IDX_PARTIE_DATEPARTIE ON partie');
        $this->addSql('DROP INDEX UNIQ_A24BE60CCF7853A4 ON phrase');
        $this->addSql('DROP INDEX IDX_A24BE60C60BB6FE6 ON phrase');
        $this->addSql('DROP INDEX IDX_A24BE60CD3DF658 ON phrase');
        $this->addSql('DROP INDEX IDX_PHRASE_DATECREATION ON phrase');
        $this->addSql('DROP INDEX IDX_PHRASE_DATEMODIFICATION ON phrase');
        $this->addSql('DROP INDEX IDX_5FB6DEC75DAA1781 ON reponse');
        $this->addSql('DROP INDEX IDX_5FB6DEC7B3E9C81 ON reponse');
        $this->addSql('DROP INDEX IDX_5FB6DEC721326CD9 ON reponse');
        $this->addSql('DROP INDEX IDX_5FB6DEC760BB6FE6 ON reponse');
        $this->addSql('DROP INDEX IDX_5FB6DEC7C6D21FCF ON reponse');
        $this->addSql('DROP INDEX IDX_REPONSE_DATEREPONSE ON reponse');
        $this->addSql('DROP INDEX IDX_REPONSE_IP ON reponse');
        $this->addSql('DROP INDEX UNIQ_57698A6A6C6E55B5 ON role');
        $this->addSql('DROP INDEX UNIQ_48C1CBCF48C1CBCF ON type_objet');
        $this->addSql('DROP INDEX UNIQ_8D1F406C8D1F406C ON type_vote');
        $this->addSql('DROP INDEX IDX_VISITE_DATEVISITE ON visite');
        $this->addSql('DROP INDEX IDX_VISITE_IP ON visite');
        $this->addSql('DROP INDEX IDX_VISITE_USERAGENT ON visite');

        $this->addSql('DROP TABLE membre_role');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP TABLE poids_reponse');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE succes');
        $this->addSql('DROP TABLE succes_membre');
        $this->addSql('DROP TABLE type_succes');

        $this->addSql('TRUNCATE groupe');

        $this->addSql('RENAME TABLE aimer_phrase TO j_aime');
        $this->addSql('RENAME TABLE vote_jugement TO vote');
        $this->addSql('ALTER TABLE vote CHANGE vote_id type_vote_id INT NOT NULL');

        $this->addSql('CREATE TABLE membre_groupe (membre_id INT NOT NULL, groupe_id INT NOT NULL, PRIMARY KEY(membre_id, groupe_id))');
        $this->addSql('CREATE TABLE role(id INT AUTO_INCREMENT PRIMARY KEY, parent_id INT NULL, name VARCHAR(255) NOT NULL)');

        $this->addSql('ALTER TABLE categorie_jugement CHANGE categorie_jugement nom VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE groupe ADD name VARCHAR(180) NOT NULL, DROP groupe_parent_id, DROP nom, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');

        $this->addSql('DELETE FROM glose WHERE auteur_id IN (SELECT id FROM membre WHERE mdp IS NULL)');
        $this->addSql('DELETE FROM membre WHERE mdp IS NULL');
        $this->addSql('DELETE FROM glose WHERE auteur_id NOT IN (SELECT id FROM membre)');
        $this->addSql('ALTER TABLE membre CHANGE pseudo username VARCHAR(180) NOT NULL, ADD username_canonical VARCHAR(180) NOT NULL, ADD email_canonical VARCHAR(180) NOT NULL, ADD salt VARCHAR(255) DEFAULT NULL, ADD renamable TINYINT(1) NOT NULL DEFAULT 0, CHANGE mdp password VARCHAR(255) NOT NULL, CHANGE date_connexion last_login DATETIME DEFAULT NULL, ADD confirmation_token VARCHAR(180) DEFAULT NULL, ADD password_requested_at DATETIME DEFAULT NULL, ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP groupe_id, DROP cle_oubli_mdp, DROP id_facebook, DROP id_twitter, CHANGE email email VARCHAR(180) NOT NULL, CHANGE sexe sexe VARCHAR(8) DEFAULT NULL, CHANGE date_naissance date_naissance DATETIME DEFAULT NULL, CHANGE commentaire_ban commentaire_ban VARCHAR(128) DEFAULT NULL, CHANGE date_deban date_deban DATETIME DEFAULT NULL, CHANGE actif enabled TINYINT(1) NOT NULL, ADD points_classement_mensuel INT NOT NULL DEFAULT 0, ADD points_classement_hebdomadaire INT NOT NULL DEFAULT 0, ADD facebook_id VARCHAR(255) DEFAULT NULL, ADD twitter_id VARCHAR(255) DEFAULT NULL, ADD google_id VARCHAR(255) DEFAULT NULL, ADD service_creation TINYINT(1) NOT NULL, ADD signale TINYINT(1) NOT NULL DEFAULT 0, CHANGE points_classement points_classement INT NOT NULL DEFAULT 0, CHANGE credits credits INT NOT NULL DEFAULT 0, DROP niveau_id');

        $this->addSql('DELETE FROM mot_ambigu_glose WHERE glose_id NOT IN (SELECT id FROM glose)');

        $this->addSql('DELETE FROM reponse WHERE auteur_id IS NULL');
        $this->addSql('DELETE FROM reponse WHERE glose_id NOT IN (SELECT id FROM glose)');
        $this->addSql('ALTER TABLE reponse ADD phrase_id INT, DROP poids_reponse_id, DROP niveau_id, CHANGE auteur_id auteur_id INT NOT NULL');

        $this->addSql('UPDATE reponse r INNER JOIN mot_ambigu_phrase map ON map.id=r.mot_ambigu_phrase_id SET r.phrase_id = map.phrase_id');
        $this->addSql('ALTER TABLE reponse CHANGE phrase_id phrase_id INT NOT NULL, DROP ip');

        $this->addSql('ALTER TABLE jugement CHANGE id_objet objet_id INT NOT NULL');

        $this->addSql('ALTER TABLE categorie_jugement ADD CONSTRAINT uc_catjug_nom UNIQUE (nom)');
        $this->addSql('ALTER TABLE glose ADD CONSTRAINT uc_glose_val UNIQUE (valeur)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT uc_grp_name UNIQUE (name)');
        $this->addSql('ALTER TABLE j_aime ADD CONSTRAINT uc_jaim_mbreidphraseid UNIQUE (membre_id, phrase_id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT uc_mbre_conftokn UNIQUE (confirmation_token)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT uc_mbre_fbid UNIQUE (facebook_id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT uc_mbre_twitid UNIQUE (twitter_id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT uc_mbre_googlid UNIQUE (google_id)');
        $this->addSql('ALTER TABLE mot_ambigu ADD CONSTRAINT uc_motamb_val UNIQUE (valeur)');
        $this->addSql('ALTER TABLE phrase ADD CONSTRAINT uc_phrase_contpur UNIQUE (contenu_pur)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT uc_rol_name UNIQUE (name)');
        $this->addSql('ALTER TABLE type_objet ADD CONSTRAINT uc_typobj_typobj UNIQUE (type_objet)');
        $this->addSql('ALTER TABLE type_vote ADD CONSTRAINT uc_typvot_typvot UNIQUE (type_vote)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT uc_vot_jugemidautid UNIQUE (jugement_id, auteur_id)');

        $this->addSql('ALTER TABLE glose ADD CONSTRAINT fk_glose_autid FOREIGN KEY (auteur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE glose ADD CONSTRAINT fk_glose_modifid FOREIGN KEY (modificateur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT fk_hist_mbreid FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE j_aime ADD CONSTRAINT fk_jaim_mbreid FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE j_aime ADD CONSTRAINT fk_jaim_phraseid FOREIGN KEY (phrase_id) REFERENCES phrase (id)');
        $this->addSql('ALTER TABLE jugement ADD CONSTRAINT fk_jugem_verdid FOREIGN KEY (verdict_id) REFERENCES type_vote (id)');
        $this->addSql('ALTER TABLE jugement ADD CONSTRAINT fk_jugem_typobjid FOREIGN KEY (type_objet_id) REFERENCES type_objet (id)');
        $this->addSql('ALTER TABLE jugement ADD CONSTRAINT fk_jugem_jugeid FOREIGN KEY (juge_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE jugement ADD CONSTRAINT fk_jugem_catjugemid FOREIGN KEY (categorie_jugement_id) REFERENCES categorie_jugement (id)');
        $this->addSql('ALTER TABLE jugement ADD CONSTRAINT fk_jugem_autid FOREIGN KEY (auteur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE mot_ambigu ADD CONSTRAINT fk_motamb_autid FOREIGN KEY (auteur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE mot_ambigu ADD CONSTRAINT fk_motamb_modifid FOREIGN KEY (modificateur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE mot_ambigu_phrase ADD CONSTRAINT fk_motambphrase_phraseid FOREIGN KEY (phrase_id) REFERENCES phrase (id)');
        $this->addSql('ALTER TABLE mot_ambigu_phrase ADD CONSTRAINT fk_motambphrase_motambid FOREIGN KEY (mot_ambigu_id) REFERENCES mot_ambigu (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT fk_part_phraseid FOREIGN KEY (phrase_id) REFERENCES phrase (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT fk_part_mbreid FOREIGN KEY (joueur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE phrase ADD CONSTRAINT fk_phrase_autid FOREIGN KEY (auteur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE phrase ADD CONSTRAINT fk_phrase_modifid FOREIGN KEY (modificateur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT fk_rep_gloseid FOREIGN KEY (glose_id) REFERENCES glose (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT fk_rep_mbreid FOREIGN KEY (auteur_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT fk_rep_motambphraseid FOREIGN KEY (mot_ambigu_phrase_id) REFERENCES mot_ambigu_phrase (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT fk_rep_phraseid FOREIGN KEY (phrase_id) REFERENCES phrase (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT fk_rol_parentid FOREIGN KEY (parent_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT fk_vot_jugid FOREIGN KEY (jugement_id) REFERENCES jugement (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT fk_vot_typvotid FOREIGN KEY (type_vote_id) REFERENCES type_vote (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT fk_vot_autid FOREIGN KEY (auteur_id) REFERENCES membre (id)');

        $this->addSql('CREATE INDEX ix_glose_autid ON glose (auteur_id)');
        $this->addSql('CREATE INDEX ix_glose_modifid ON glose (modificateur_id)');
        $this->addSql('CREATE INDEX ix_glose_dtcreat ON glose (date_creation)');
        $this->addSql('CREATE INDEX ix_glose_dtmodif ON glose (date_modification)');
        $this->addSql('CREATE INDEX ix_hist_val ON historique (valeur)');
        $this->addSql('CREATE INDEX ix_hist_mbreid ON historique (membre_id)');
        $this->addSql('CREATE INDEX ix_hist_dtact ON historique (date_action)');
        $this->addSql('CREATE INDEX ix_jaim_mbreid ON j_aime (membre_id)');
        $this->addSql('CREATE INDEX ix_jaim_phraseid ON j_aime (phrase_id)');
        $this->addSql('CREATE INDEX ix_jaim_dtcreat ON j_aime (date_creation)');
        $this->addSql('CREATE INDEX ix_jugem_verdid ON jugement (verdict_id)');
        $this->addSql('CREATE INDEX ix_jugem_typobjid ON jugement (type_objet_id)');
        $this->addSql('CREATE INDEX ix_jugem_jugeid ON jugement (juge_id)');
        $this->addSql('CREATE INDEX ix_jugem_catjugemid ON jugement (categorie_jugement_id)');
        $this->addSql('CREATE INDEX ix_jugem_autid ON jugement (auteur_id)');
        $this->addSql('CREATE INDEX ix_jugem_dtcreat ON jugement (date_creation)');
        $this->addSql('CREATE INDEX ix_jugem_dtdelib ON jugement (date_deliberation)');
        $this->addSql('CREATE INDEX ix_jugem_objid ON jugement (objet_id)');
        $this->addSql('CREATE INDEX ix_mbre_ptsclasheb ON membre (points_classement_hebdomadaire)');
        $this->addSql('CREATE INDEX ix_mbre_ptsclasmen ON membre (points_classement_mensuel)');
        $this->addSql('CREATE INDEX ix_mbre_cred ON membre (credits)');
        $this->addSql('CREATE INDEX ix_mbre_dtinscr ON membre (date_inscription)');
        $this->addSql('CREATE INDEX ix_mbre_dtnaiss ON membre (date_naissance)');
        $this->addSql('CREATE INDEX ix_mbre_ptsclas ON membre (points_classement)');
        $this->addSql('CREATE INDEX IDX_9EB019986A99F74A ON membre_groupe (membre_id)');
        $this->addSql('CREATE INDEX IDX_9EB019987A45358C ON membre_groupe (groupe_id)');
        $this->addSql('CREATE INDEX ix_motamb_autid ON mot_ambigu (auteur_id)');
        $this->addSql('CREATE INDEX ix_motamb_modifid ON mot_ambigu (modificateur_id)');
        $this->addSql('CREATE INDEX ix_motamb_dtcreat ON mot_ambigu (date_creation)');
        $this->addSql('CREATE INDEX ix_motamb_dtmodif ON mot_ambigu (date_modification)');
        $this->addSql('CREATE INDEX ix_part_phraseid ON partie (phrase_id)');
        $this->addSql('CREATE INDEX ix_part_joueurid ON partie (joueur_id)');
        $this->addSql('CREATE INDEX ix_part_dtpart ON partie (date_partie)');
        $this->addSql('CREATE INDEX ix_part_gainjoueur ON partie (gain_joueur)');
        $this->addSql('CREATE INDEX ix_phrase_gaincreat ON phrase (gain_createur)');
        $this->addSql('CREATE INDEX ix_phrase_autid ON phrase (auteur_id)');
        $this->addSql('CREATE INDEX ix_phrase_modifid ON phrase (modificateur_id)');
        $this->addSql('CREATE INDEX ix_phrase_dtcreat ON phrase (date_creation)');
        $this->addSql('CREATE INDEX ix_phrase_dtmodif ON phrase (date_modification)');
        $this->addSql('CREATE INDEX ix_rep_phraseid ON reponse (phrase_id)');
        $this->addSql('CREATE INDEX ix_rep_gloseid ON reponse (glose_id)');
        $this->addSql('CREATE INDEX ix_rep_auteurid ON reponse (auteur_id)');
        $this->addSql('CREATE INDEX ix_rep_motambphraseid ON reponse (mot_ambigu_phrase_id)');
        $this->addSql('CREATE INDEX ix_rep_dtrep ON reponse (date_reponse)');
        $this->addSql('CREATE INDEX ix_rol_parentid ON role (parent_id)');
        $this->addSql('CREATE INDEX ix_vot_jugid ON vote (jugement_id)');
        $this->addSql('CREATE INDEX ix_vot_typvotid ON vote (type_vote_id)');
        $this->addSql('CREATE INDEX ix_vot_autid ON vote (auteur_id)');
        $this->addSql('CREATE INDEX ix_vot_dtcreat ON vote (date_creation)');
        $this->addSql('CREATE INDEX ix_vot_dtmodif ON vote (date_modification)');
        $this->addSql('CREATE INDEX ix_visit_dtvisit ON visite (date_visite)');
        $this->addSql('CREATE INDEX ix_visit_ip ON visite (ip)');
        $this->addSql('CREATE INDEX ix_visit_useragent ON visite (user_agent)');

        $this->addSql('INSERT INTO groupe(id, name, roles) VALUES(1, "Administrateur", \'a:1:{i:0;s:19:"ROLE_ADMINISTRATEUR";}\')');
        $this->addSql('INSERT INTO groupe(id, name, roles) VALUES(2, "Modérateur", \'a:1:{i:0;s:15:"ROLE_MODERATEUR";}\')');
        $this->addSql('INSERT INTO groupe(id, name, roles) VALUES(3, "Membre", \'a:1:{i:0;s:9:"ROLE_USER";}\')');
        $this->addSql('INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(48, 1)');
        $this->addSql('INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(58, 1)');
        $this->addSql('INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(69, 1)');
        $this->addSql('INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(71, 1)');
        $this->addSql('INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(72, 1)');
        $this->addSql('INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(74, 1)');
        $this->addSql('INSERT INTO role(id, parent_id, name) VALUES(1, null, \'ROLE_SUPER_ADMIN\')');
        $this->addSql('INSERT INTO role(id, parent_id, name) VALUES(2, 1, \'ROLE_ADMINISTRATEUR\')');
        $this->addSql('INSERT INTO role(id, parent_id, name) VALUES(3, 2, \'ROLE_MODERATEUR\')');
        $this->addSql('INSERT INTO role(id, parent_id, name) VALUES(4, 3, \'ROLE_USER\')');
        $this->addSql('INSERT INTO membre_groupe SELECT m.id, 3 FROM membre m WHERE m.id not in (SELECT membre_id FROM membre_groupe)');

        $this->addSql('UPDATE membre SET roles = "a:0:{}"');
        $this->addSql('UPDATE membre SET roles = \'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}\' WHERE username = \'alex\'');

        $this->addSql('DELETE FROM jugement WHERE type_objet_id = 5');
        $this->addSql('DELETE FROM type_objet WHERE id = 5');
        $this->addSql('DELETE FROM jugement WHERE type_objet_id = 1');
        $this->addSql('DELETE FROM type_objet WHERE id = 1');
    }

    public function down(Schema $schema) : void
    {

    }
}
