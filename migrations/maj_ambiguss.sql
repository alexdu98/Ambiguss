DROP TABLE membre_role;
DROP TABLE role;

SET FOREIGN_KEY_CHECKS=0;
DELETE FROM membre WHERE mdp IS NULL;
SET FOREIGN_KEY_CHECKS=1;

ALTER TABLE membre 
CHANGE pseudo username VARCHAR(180) NOT NULL, 
ADD username_canonical VARCHAR(180) NOT NULL, 
ADD email_canonical VARCHAR(180) NOT NULL, 
ADD salt VARCHAR(255) DEFAULT NULL,
ADD renamable TINYINT(1) NOT NULL,
CHANGE mdp password VARCHAR(255) NOT NULL, 
CHANGE date_connexion last_login DATETIME DEFAULT NULL, 
ADD confirmation_token VARCHAR(180) DEFAULT NULL, 
ADD password_requested_at DATETIME DEFAULT NULL, 
ADD roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', 
DROP groupe_id, 
DROP cle_oubli_mdp, 
DROP id_facebook, 
DROP id_twitter, 
CHANGE email email VARCHAR(180) NOT NULL, 
CHANGE sexe sexe VARCHAR(8) DEFAULT NULL, 
CHANGE date_naissance date_naissance DATETIME DEFAULT NULL, 
CHANGE commentaire_ban commentaire_ban VARCHAR(128) DEFAULT NULL, 
CHANGE date_deban date_deban DATETIME DEFAULT NULL, 
CHANGE actif enabled TINYINT(1) NOT NULL;

UPDATE membre SET roles = "a:0:{}";
UPDATE membre SET roles = 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}' WHERE username = 'alex';

DELETE FROM groupe;

ALTER TABLE groupe 
ADD name VARCHAR(180) NOT NULL, 
DROP groupe_parent_id,
DROP nom, 
DROP roles,
ADD roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)';

INSERT INTO groupe(id, name, roles) VALUES(1, "Administrateur", 'a:1:{i:0;s:19:"ROLE_ADMINISTRATEUR";}');
INSERT INTO groupe(id, name, roles) VALUES(2, "Modérateur", 'a:1:{i:0;s:15:"ROLE_MODERATEUR";}');
INSERT INTO groupe(id, name, roles) VALUES(3, "Membre", 'a:1:{i:0;s:9:"ROLE_USER";}');

INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(48, 1);
INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(58, 1);
INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(69, 1);
INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(71, 1);
INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(72, 1);
INSERT INTO membre_groupe(membre_id, groupe_id) VALUES(74, 1);

INSERT INTO role(id, parent_id, name) VALUES(1, null, 'ROLE_SUPER_ADMIN');
INSERT INTO role(id, parent_id, name) VALUES(2, 1, 'ROLE_ADMINISTRATEUR');
INSERT INTO role(id, parent_id, name) VALUES(3, 2, 'ROLE_MODERATEUR');
INSERT INTO role(id, parent_id, name) VALUES(4, 3, 'ROLE_USER');

INSERT INTO membre_groupe SELECT m.id, 3 FROM membre m WHERE m.id not in (SELECT membre_id FROM membre_groupe);

ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29B3E9C81;
ALTER TABLE membre
DROP niveau_id;

ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC75DAA1781;
ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7B3E9C81;
ALTER TABLE reponse 
DROP poids_reponse_id,
DROP niveau_id;

DROP TABLE poids_reponse;
DROP TABLE succes_membre;
DROP TABLE succes;
DROP TABLE type_succes;
DROP TABLE niveau;

RENAME TABLE aimer_phrase TO j_aime;
RENAME TABLE vote_jugement TO vote;

DELETE FROM reponse WHERE auteur_id IS NULL;
UPDATE reponse r INNER JOIN mot_ambigu_phrase map ON map.id=r.mot_ambigu_phrase_id SET r.phrase_id = map.phrase_id;

CREATE UNIQUE INDEX UNQ_MAP_PHRASE_ORDRE ON mot_ambigu_phrase (phrase_id, ordre);
