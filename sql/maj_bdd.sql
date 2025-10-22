CREATE DATABASE `relais-managers`;

USE `relais-managers`;

SET SQL_SAFE_UPDATES = 0;

CREATE TABLE `users` (
                         `id` INT AUTO_INCREMENT PRIMARY KEY,
                         `lastname` VARCHAR(100) DEFAULT NULL,
                         `firstname` VARCHAR(100) DEFAULT NULL,
                         `identifier` VARCHAR(10) DEFAULT NULL,
                         `email` VARCHAR(150) DEFAULT NULL,
                         `password` VARCHAR(255) DEFAULT NULL,
                         `remember_token` VARCHAR(100) DEFAULT NULL,
                         `confirmation_token` VARCHAR(100) DEFAULT NULL,
                         `confirmed_at` TIMESTAMP NULL DEFAULT NULL,
                         `reset_token` VARCHAR(100) DEFAULT NULL,
                         `reset_at` TIMESTAMP NULL DEFAULT NULL,
                         `role` VARCHAR(50) DEFAULT NULL,
                         `last_ip` VARCHAR(45) DEFAULT NULL, -- Pour supporter IPv4 et IPv6
                         `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         `last_connection_at` TIMESTAMP NULL DEFAULT NULL,
                         `group_id` INT NULL
);

ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_groupe`
        FOREIGN KEY (`group_id`) REFERENCES `groupes`(`id`)
            ON DELETE SET NULL;

INSERT INTO `users` (`lastname`, `firstname`, `identifier`, `email`, `password`, `remember_token`, `confirmation_token`, `confirmed_at`, `reset_token`, `reset_at`, `role`, `last_ip`, `created_at`, `last_connection_at`)
VALUES
    ('Doe', 'John', 'A1B2C3D4E5', 'jdoe@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 1, '192.168.0.1', NOW(), NULL),
    ('Smith', 'Alice', 'F6G7H8I9J1', 'asmith@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.2', NOW(), NULL),
    ('Wayne', 'Bruce', 'K2L3M4N5O6', 'bwayne@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 3, '192.168.0.3', NOW(), NULL),
    ('Kent', 'Clark', 'P7Q8R9S1T2', 'ckent@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.4', NOW(), NULL),
    ('Danvers', 'Diana', 'U3V4W5X6Y7', 'ddanvers@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.5', NOW(), NULL),
    ('Hudson', 'Eve', 'Z8A9B1C2D3', 'ehudson@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.6', NOW(), NULL),
    ('Green', 'Fred', 'E4F5G6H7I8', 'fgreen@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.7', NOW(), NULL),
    ('Lee', 'Helen', 'J9K1L2M3N4', 'hlee@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.8', NOW(), NULL),
    ('Jones', 'Ian', 'O5P6Q7R8S9', 'ijones@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.9', NOW(), NULL),
    ('Doe', 'Karen', 'T1U2V3W4X5', 'kdoe@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.10', NOW(), NULL),
    ('Grey', 'Logan', 'Y6Z7A8B9C1', 'lgrey@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.11', NOW(), NULL),
    ('Scott', 'Michael', 'D2E3F4G5H6', 'mscott@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.12', NOW(), NULL),
    ('Parker', 'Nina', 'I7J8K9L1M2', 'nparker@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.13', NOW(), NULL),
    ('Brown', 'Oscar', 'N3O4P5Q6R7', 'obrown@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.14', NOW(), NULL),
    ('Green', 'Paul', 'S8T9U1V2W3', 'pgreen@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.15', NOW(), NULL),
    ('White', 'Rebecca', 'X4Y5Z6A7B8', 'rwhite@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 2, '192.168.0.16', NOW(), NULL),
    ('Jones', 'Steve', 'C9D1E2F3G4', 'sjones@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.17', NOW(), NULL),
    ('Lee', 'Tom', 'H5I6J7K8L9', 'tlee@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.18', NOW(), NULL),
    ('Wilson', 'Ursula', 'M1N2O3P4Q5', 'uwilson@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 3, '192.168.0.19', NOW(), NULL),
    ('Clark', 'Victor', 'R6S7T8U9V1', 'vclark@example.com', 'password123', NULL, NULL, NULL, NULL, NULL, 5, '192.168.0.20', NOW(), NULL);

CREATE TABLE `groupes` (
                           `id` INT AUTO_INCREMENT PRIMARY KEY,
                           `groupe_name` VARCHAR(255) NOT NULL,
                           `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `groupes` (`groupe_name`, `created_at`) VALUES
                                                        ('Google', '2024-10-14 13:11:54'),
                                                        ('Microsoft', '2024-10-14 13:11:54'),
                                                        ('Apple', '2024-10-14 13:11:54'),
                                                        ('Amazon', '2024-10-14 13:11:54'),
                                                        ('Facebook', '2024-10-14 13:11:54'),
                                                        ('Tesla', '2024-10-14 13:11:54'),
                                                        ('IBM', '2024-10-14 13:11:54'),
                                                        ('Oracle', '2024-10-14 13:11:54'),
                                                        ('Netflix', '2024-10-14 13:11:54'),
                                                        ('Salesforce', '2024-10-14 13:11:54'),
                                                        ('RIOT GAMES', '2024-10-14 16:18:04');

CREATE TABLE template_quiz_question (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        quiz_type VARCHAR(5) NOT NULL,
                                        question_type VARCHAR(20) NOT NULL,
                                        label VARCHAR(2000) NOT NULL,
                                        label_auto VARCHAR(2000) NOT NULL,
                                        ordre INT NOT NULL,
                                        response_required TINYINT(1) NOT NULL,
                                        report_ordre INT,
                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        editable INT DEFAULT 1,
                                        deletable INT DEFAULT 1
);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre) VALUES
    ('360','INPUT-RADIO','<div style="font-size: 32px; color: #ff6600;">Votre position par rapport à la personne évaluée :</div><div style="font-size: 16px; color: #696252;"><span>A. Équipe</span><span style="margin-left:20px">B. Transverse</span><span style="margin-left:20px">C. Hiérarchique</span></div>',
     '',2, 1, NULL);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre) VALUES
    ('360','TEXT','<div style="font-size: 32px; color: #ff6600;font-family:Trebuchet MS">Démarrage du 360</div><div style="font-size: 16px; color: #ff6600;">Code réponses</div><div style="font-size: 16px; color: #696252;">0 : Ce n’est pas lui</div><div style="font-size: 16px; color: #696252;">1 : Ce n’est pas vraiment lui</div><div style="font-size: 16px; color: #696252;">2 : C’est parfois lui</div><div style="font-size: 16px; color: #696252;">3 : C’est plutôt lui</div><div style="font-size: 16px; color: #696252;">4 : C’est tout à fait lui</div>',
     '<div style="font-size: 32px; color: #ff6600;font-family:Trebuchet MS">Démarrage du 360</div><div style="font-size: 16px; color: #ff6600;">Code réponses</div><div style="font-size: 16px; color: #696252;">0 : Ce n’est pas moi</div><div style="font-size: 16px; color: #696252;">1 : Ce n’est pas vraiment moi</div><div style="font-size: 16px; color: #696252;">2 : C’est parfois moi</div><div style="font-size: 16px; color: #696252;">3 : C’est plutôt moi</div><div style="font-size: 16px; color: #696252;">4 : C’est tout à fait moi</div>',3, 0, NULL);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre) VALUES
    ('360','CHAPTER','<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">AVEC SES ÉQUIPES</div>',
     '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">AVEC MES ÉQUIPES</div>',4, 0, NULL);


INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Explique clairement :<div><ul><li>le projet et la stratégie d’entreprise, ses évolutions</li><li>Les missions, les tâches à accomplir ; les périmètres de responsabilités et d’autonomie ; les objectifs à atteindre</li><li>Les règles à respecter</li><li>Les règles qui régissent la collaboration du salarié avec l’entreprise (exemples : rémunération, gratifications, sanctions, possibilités ou non d’évolutions professionnelles...)</li><li>Les contraintes à prendre en compte</li><li>Les changements à prendre en compte, à mettre en œuvre (macros, micros)</li></ul></div></div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’explique clairement :<div><ul><li>le projet et la stratégie d’entreprise, ses évolutions</li><li>Les missions, les tâches à accomplir ; les périmètres de responsabilités et d’autonomie ; les objectifs à atteindre</li><li>Les règles à respecter</li><li>Les règles qui régissent la collaboration du salarié avec l’entreprise (exemples : rémunération, gratifications, sanctions, possibilités ou non d’évolutions professionnelles...)</li><li>Les contraintes à prendre en compte</li><li>Les changements à prendre en compte, à mettre en œuvre (macros, micros)</li></ul></div></div>',5, 1, 1),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Supervise, évalue (ni trop ni pas assez) :<div><ul><li>Le travail réalisé</li><li>Les résultats obtenus</li><li>La qualité de la collaboration au sein de l’équipe et avec les autres services</li><li>La Qualité de Vie au Travail (Q.V.T.)</li></ul></div></div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je supervise, évalue (ni trop ni pas assez) :<div><ul><li>Le travail réalisé</li><li>Les résultats obtenus</li><li>La qualité de la collaboration au sein de l’équipe et avec les autres services</li><li>La Qualité de Vie au Travail (Q.V.T.)</li></ul></div></div>',6, 1, 2),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">A des échanges réguliers, individuels, collectifs, pour faire le point sur :<div><ul><li>ce qui va, ne va pas dans le travail, les difficultés rencontrées</li><li>savoir comment chacun va</li></ul></div></div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai des échanges réguliers, individuels, collectifs, pour faire le point sur :<div><ul><li>ce qui va, ne va pas dans le travail, les difficultés rencontrées</li><li>savoir comment chacun va</li></ul></div></div>',7, 1, 3),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Se soucie tout autant de la performance que de la Q.V.T.</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me soucie tout autant de la performance que de la Q.V.T.</div>', 8, 1, 4),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Cherche, avant de réagir, à comprendre :<div><ul><li>les raisons d’écarts entre le travail demandé et celui réalisé</li><li>les raisons de problèmes qui se répètent</li></ul></div></div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je cherche, avant de réagir, à comprendre :<div><ul><li>les raisons d’écarts entre le travail demandé et celui réalisé</li><li>les raisons de problèmes qui se répètent</li></ul></div></div>',9, 1, 5),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Distingue les situations qu’il lui appartient de résoudre de celles qui appartiennent à l’équipe ou à un travail collectif</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je distingue les situations qu’il m’appartient de résoudre de celles qui appartiennent à l’équipe ou à un travail collectif</div>',10, 1, 6),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">S’appuie sur – et incite à – l’intelligence collective pour trouver des solutions, plutôt que de prendre en charge et résoudre seul</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je m’appuie sur – et J’incite à – l’intelligence collective pour trouver des solutions, plutôt que de prendre en charge et résoudre seul</div>',11, 1, 7);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES ('360', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">AVEC SA HIERARCHIE</div>',
        '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">AVEC MA HIERARCHIE</div>',12, 0, NULL);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">A une vision claire :<div><ul><li>des rôles et responsabilités de chacun (les siens, ceux de sa hiérarchie)</li><li>de son rôle d’interface entre son équipe et sa hiérarchie</li></ul></div></div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai une vision claire :<div><ul><li>des rôles et responsabilités de chacun (les miens, ceux de ma hiérarchie)</li><li>de son rôle d’interface entre mon équipe et ma hiérarchie</li></ul></div></div>',13, 1, 8),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Fait des états des lieux clairs avec sa hiérarchie. Expose clairement à celle-ci ses éventuels besoins, difficultés, limites (les siens, ceux de son équipe) plutôt que de vouloir montrer que tout va bien ou vouloir tout assumer, tout résoudre seul</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je fais des états des lieux clairs avec ma hiérarchie. J’expose clairement à celle-ci mes éventuels besoins, difficultés, limites (les miens, ceux de mon équipe) plutôt que de vouloir montrer que tout va bien ou vouloir tout assumer, tout résoudre seul</div>',14, 1, 9),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Prend en compte, avec ouverture d’esprit, le réel de sa hiérarchie : ses objectifs, ses contraintes, ses limites</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je prends en compte, avec ouverture d’esprit, le réel de ma hiérarchie : ses objectifs, ses contraintes, ses limites</div>',15, 1, 10),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Différencie dans les problèmes à résoudre, ceux qu’il lui appartient de solutionner, ceux qui appartiennent à sa hiérarchie, ceux qui leur sont communs</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je différencie dans les problèmes à résoudre, ceux qu’il m’appartient de solutionner, ceux qui appartiennent à ma hiérarchie, ceux qui nous sont communs</div>',16, 1, 11),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Provoque des moments d’échange avec sa hiérarchie pour trouver des solutions quand un sujet le nécessite ou dépasse ses compétences ou son périmètre</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je provoque des moments d’échange avec ma hiérarchie pour trouver des solutions quand un sujet le nécessite ou dépasse mes compétences ou mon périmètre</div>',17, 1, 12);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES ('360', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">AVEC LES ACTEURS TRANSVERSES</div>',
        '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">AVEC LES ACTEURS TRANSVERSES</div>', 18, 0, NULL);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">A une vision claire :<div><ul><li>des rôles et responsabilités, périmètres de chacun (les siens, ceux de ses interlocuteurs),</li><li>de son rôle d’interface entre son équipe et les services ou acteurs transverses</li></ul></div></div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai une vision claire :<div><ul><li>des rôles et responsabilités, périmètres de chacun (les miens, ceux de mes interlocuteurs),</li><li>de mon rôle d’interface entre mon équipe et les services ou acteurs transverses</li></ul></div></div>',19, 1, 13),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Fait des états des lieux clairs, avec les acteurs transverses avec lesquels lui ou son équipe collabore. Expose clairement ses éventuels besoins, difficultés rencontrées, limites, plutôt que vouloir tout assumer ou demander à son équipe de faire avec</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je fais des états des lieux clairs, avec les acteurs transverses avec lesquels moi ou mon équipe collabore. J’expose clairement mes éventuels besoins, difficultés rencontrées, limites, plutôt que vouloir tout assumer ou demander à mon équipe de faire avec</div>',20, 1, 14),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Prend en compte, avec ouverture d’esprit, le réel de ses interlocuteurs : leurs objectifs, leurs contraintes, leurs limites</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je prends en compte, avec ouverture d’esprit, le réel de mes interlocuteurs : leurs objectifs, leurs contraintes, leurs limites</div>',21, 1, 15),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Différencie, dans les problèmes à résoudre, ceux qu’il lui appartient de solutionner, ceux qui appartiennent à ses interlocuteurs, ceux qui leur sont communs</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je différencie, dans les problèmes à résoudre, ceux qu’il lui m’appartient de solutionner, ceux qui appartiennent à mes interlocuteurs, ceux qui nous sont communs</div>',22, 1, 16),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Provoque des moments d’échange avec eux pour trouver des solutions à ce qui pose question ou problème et nécessite de collaborer</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je provoque des moments d’échange avec eux pour trouver des solutions à ce qui pose question ou problème et nécessite qu’on collabore</div>',23, 1, 17);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES ('360', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">GESTION DE SOI, DE SON LEADERSHIP</div>',
        '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">GESTION DE SOI, DE MON LEADERSHIP</div>',24, 0, NULL);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quels que soient ses interlocuteurs - équipe, collègues, hiérarchiques... - aborde clairement ses attendus, ses besoins, les problèmes rencontrés dans la collaboration.. ceci par opposition à « ne pas dire », à « garder pour soi » ou à en parler avec d’autres mais qui ne sont pas les intéressés</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quels que soient mes interlocuteurs - équipe, collègues, hiérarchiques... - j’aborde clairement mes attendus, mes besoins, les problèmes rencontrés dans la collaboration... ceci par opposition à « ne pas dire », à « garder pour soi » ou à en parler avec d’autres mais qui ne sont pas les intéressés</div>',25, 1, 18),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait écouter les avis, les ressentis, les objections... avec ouverture, empathie, objectivité</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais écouter les avis, les ressentis, les objections... avec ouverture, empathie, objectivité</div>',26, 1, 19),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait provoquer une co-analyse d’une situation qui pose question ou problème, des causes et des solutions possibles, plutôt qu’une analyse à partir de son seul point de vue</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais provoquer une co-analyse d’une situation qui pose question ou problème, des causes et des solutions possibles, plutôt qu’une analyse à partir de mon seul point de vue</div>',27, 1, 20),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait identifier les situations qui nécessitent de s’appuyer sur d’autres personnes et compétences pour analyser, comprendre et résoudre, plutôt que de ne compter que sur soi, s’acharner, ne pas voir ses limites, risquer de perdre du temps là où d’autres peuvent en faire gagner...</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais identifier les situations qui nécessitent de s’appuyer sur d’autres personnes et compétences pour analyser, comprendre et résoudre, plutôt que de ne compter que sur moi, m’acharner, ne pas voir mes limites, risquer de perdre du temps là où d’autres peuvent en faire gagner...</div>',28, 1, 21),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait distinguer, dans ce qui pose problème ou question, ce qui lui appartient de résoudre, ce qui appartient à ses interlocuteurs, ce qui nécessite de réfléchir et résoudre avec eux</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais distinguer, dans ce qui pose problème ou question, ce qui m’appartient de résoudre, ce qui appartient à ses interlocuteurs, ce qui nécessite de réfléchir et résoudre ensemble</div>',29, 1, 22);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait parler avec les personnes concernées des désaccords, tensions constatées ; sait provoquer une co-réflexion sur les raisons et ce qu’il y a lieu de faire</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais parler avec les personnes concernées des désaccords, tensions constatées ; je sais provoquer une co-réflexion sur les raisons et ce qu’il y a lieu de faire</div>',30, 1, 23),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait constater une situation bloquée. Sait analyser, mesurer les enjeux. Sait opter pour la posture la plus pertinente (renoncer, s’adapter, s’appuyer sur d’autres acteurs, contraindre mais sans agressivité ni posture dominante...)</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais constater une situation bloquée. je sais l’analyser, mesurer les enjeux. Je sais opter pour la posture la plus pertinente (renoncer, s’adapter, s’appuyer sur d’autres acteurs, contraindre mais sans agressivité ni posture dominante...)</div>',31, 1, 24),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait mettre en place une relation d’interdépendance (donnant/donnant ; gagnant/gagnant) avec son entourage</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais mettre en place une relation d’interdépendance (donnant/donnant ; gagnant/gagnant) avec mon entourage</div>',32, 1, 25),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait distinguer ce qui peut être changé, résolu, de ce qui ne peut l’être. Agit pour que soit changé et résolu ce qui peut l’être. Accepte ce qui ne peut l’être et cherche à faire au mieux à partir de cette réalité</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais distinguer ce qui peut être changé, résolu, de ce qui ne peut l’être. Agit pour que soit changé et résolu ce qui peut l’être. J’accepte ce qui ne peut l’être et cherche à faire au mieux à partir de cette réalité</div>',33, 1, 26),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">N’a ni tendance à trop d’optimisme, ni tendance au pessimisme. Est plutôt réaliste</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n’ai ni tendance à trop d’optimisme, ni tendance au pessimisme. Je suis plutôt réaliste</div>',34, 1, 27),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Est ouvert, curieux, confiant plutôt que fermé, contrôlant, méfiant</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis ouvert, curieux, confiant plutôt que fermé, contrôlant, méfiant</div>',35, 1, 28),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait gérer sa frustration, rebondir</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais gérer sa frustration, rebondir</div>',36, 1, 29),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait différencier « opiniâtreté » et « acharnement »</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais différencier « opiniâtreté » et « acharnement »</div>',37, 1, 30),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait se remettre en question (ni trop, ni pas assez)</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais me remettre en question (ni trop, ni pas assez)</div>',38, 1, 31),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Est un référent apprécié, respecté, plutôt « suivi » par ses équipes</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis un référent apprécié, respecté, plutôt « suivi » par mes équipes</div>',39, 1, 32),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Est un collaborateur apprécié, crédible, plutôt pris en compte ou « suivi » par sa hiérarchie</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis un collaborateur apprécié, crédible, plutôt pris en compte ou « suivi » par ma hiérarchie</div>',40, 1, 33),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Est plutôt considéré par son environnement comme : clair, cohérent, réaliste, affirmé dans ses objectifs, ses demandes ; à l’écoute ; privilégiant la co-réflexion à la pensée unique ; capable de se remettre en question ; prenant des positions réfléchies, fondées, justes ; opiniâtre et non têtu ; humain et bienveillant mais exigeant ; ayant un leadership éclairé</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis plutôt considéré par mon environnement comme : clair, cohérent, réaliste, affirmé dans mes objectifs, mes demandes ; à l’écoute ; privilégiant la co-réflexion à la pensée unique ; capable de me remettre en question ; prenant des positions réfléchies, fondées, justes ; opiniâtre et non têtu ; humain et bienveillant mais exigeant ; ayant un leadership éclairé</div>',41, 1, 34);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES ('360', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">GESTION DE SON TEMPS, DE SES PRIORITÉS</div>',
        '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">GESTION DE MON TEMPS, DE MES PRIORITÉS</div>',42, 0, NULL);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre)
VALUES
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">A une vision claire de ses rôles et responsabilités, de ce que chacun doit faire</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai une vision claire de ses rôles et responsabilités, de ce que chacun doit faire</div>',43, 1, 35),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">S’assure que chacun assume ses rôles et responsabilités ; « refuse » de compenser</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je m’assure que chacun assume ses rôles et responsabilités ; je « refuse » de compenser</div>',44, 1, 36),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Observe les tâches ou missions à réaliser mais non attribuées ; fait en sorte qu’elles soient attribuées ; n’attrape pas tout ce qui « traîne »</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’observe les tâches ou missions à réaliser mais non attribuées ; je fais en sorte qu’elles soient attribuées ; je n’attrape pas tout ce qui « traîne »</div>',45, 1, 37),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Connait ses réelles priorités ; sait prioriser</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je connais mes réelles priorités ; je sais prioriser</div>', 46, 1, 38),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait distinguer l’important de l’urgent</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais distinguer l’important de l’urgent</div>',47, 1, 39),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Repère et élimine ses croque-temps : manque d’organisation ; pertes de temps ; perfectionnisme ; besoin de faire plaisir...</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je repère et élimine mes croque-temps : manque d’organisation ; pertes de temps ; perfectionnisme ; besoin de faire plaisir...</div>', 48, 1, 40),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Est organisé, rigoureux</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis organisé, rigoureux</div>',49, 1, 41),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait déléguer</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais déléguer</div>',50, 1, 42),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait poser des limites, dire non</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais poser des limites, dire non</div>',51, 1, 43),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait récupérer, est attentif à ne pas être dans le surinvestissement, sait garder un équilibre entre sa vie privée et sa vie professionnelle</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais récupérer, je suis attentif à ne pas être dans le surinvestissement, je sais garder un équilibre entre ma vie privée et ma vie professionnelle</div>',52, 1, 44),
    ('360', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Sait ne pas se mettre en surtress</div>',
     '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais ne pas se mettre en surtress</div>',53, 1, 45);

INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre, editable, deletable)
VALUES
-- CHAPTER: 1- LE TRAVAIL DEMANDÉ
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">LE TRAVAIL DEMANDÉ</div>', '', 1, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une bonne vision du projet de l\'entreprise (les résultats qu\'elle veut atteindre, les contraintes qu\'elle doit prendre en compte, les améliorations et projets qu\'elle veut mettre en oeuvre, les raisons...)</div>', '', 2, 1, 1, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une bonne connaissance de ce qu\'on attend de moi, de mon secteur (les missions, les tâches à effectuer, les résultats à atteindre, les règles à respecter, les critères sur lesquels je suis évalué...)</div>', '', 3, 1, 2, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Les instructions ou demandes que je reçois sont suffisamment claires pour pouvoir y répondre</div>', '', 4, 1, 3, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une bonne connaissance et compréhension de ce qu\'on veut que je fasse progresser dans mes façons de travailler</div>', '', 5, 1, 4, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une bonne connaissance des changements auxquels je dois m\'adapter ou contribuer et de leurs raisons (travail, organisation du travail, méthodes, matériel, règles...)</div>', '', 6, 1, 5, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">En général, je connais les raisons des directives, des demandes que je reçois. Ce à quoi je dois contribuer me paraît fondé, justifié</div>', '', 7, 1, 6, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une bonne vision des primes possibles, des évolutions de fonction ou de salaire possibles. Je sais ce qui me permettra de les obtenir</div>', '', 8, 1, 7, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Ma fiche de poste reflète bien ce que je fais au quotidien</div>', '', 9, 1, 8, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Globalement les rémunérations, primes, évolutions de fonctions ou de salaires sont fondées et attribuées de façon juste, équitable</div>', '', 10, 1, 9, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je connais les modes d\'évaluation de mon travail. Ils sont objectifs et pertinents</div>', '', 11, 1, 10, 1, 0),

-- CHAPTER: 2- MA PERCEPTION DU TRAVAIL
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">MA PERCEPTION DU TRAVAIL</div>', '', 12, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je ne rencontre jamais aucune difficulté à réaliser le travail qu\'on attend de moi</div>', '', 13, 1, 11, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense que quand on veut, on peut toujours</div>', '', 14, 1, 12, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'estime que je dois assurer mon travail sans avoir besoin de soutien</div>', '', 15, 1, 13, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense que les faibles ont des excuses, les forts ont des solutions</div>', '', 16, 1, 14, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Pour moi, travailler, c\'est faire ce qui nous est demandé, sans objection, sans se plaindre</div>', '', 17, 1, 15, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'agis sans réfléchir à mes difficultés, sans regarder ce qui me va, ce qui ne me va pas dans mon travail</div>', '', 18, 1, 16, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Lorsqu\'une situation est difficile, il n\'y a pas à se poser de question. Il faut faire avec</div>', '', 19, 1, 17, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai le sentiment que ça ne sert à rien de dire ce qui pose problème, que personne ne s\'en soucie et/ou que ça peut être mal vu</div>', '', 20, 1, 18, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Parler des difficultés rencontrées dans son travail se fait très peu dans mon entreprise</div>', '', 21, 1, 19, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Parler des difficultés personnelles se fait très peu dans mon entreprise</div>', '', 22, 1, 20, 1, 0),

-- CHAPTER: 3 - LA GESTION DES SITUATIONS RENCONTRÉES
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">LA GESTION DES SITUATIONS RENCONTRÉES</div>', '', 23, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je ne rencontre jamais aucune difficulté à réaliser le travail qu\'on attend de moi</div>', '', 24, 1, 21, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense que quand on veut, on peut toujours</div>', '', 25, 1, 22, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'estime que je dois assurer mon travail sans avoir besoin de soutien</div>', '', 26, 1, 23, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense que les faibles ont des excuses, les forts ont des solutions</div>', '', 27, 1, 24, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Pour moi, travailler, c\'est faire ce qui nous est demandé, sans objection, sans se plaindre</div>', '', 28, 1, 25, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'agis sans réfléchir à mes difficultés, sans regarder ce qui me va, ce qui ne me va pas dans mon travail</div>', '', 29, 1, 26, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Lorsqu\'une situation est difficile, il n\'y a pas à se poser de question. Il faut faire avec</div>', '', 30, 1, 27, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai le sentiment que ça ne sert à rien de dire ce qui pose problème, que personne ne s\'en soucie et/ou que ça peut être mal vu</div>', '', 31, 1, 28, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Parler des difficultés rencontrées dans son travail se fait très peu dans mon entreprise</div>', '', 32, 1, 29, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Parler des difficultés personnelles se fait très peu dans mon entreprise</div>', '', 33, 1, 30, 1, 0),

-- CHAPTER: 4. La relation avec les autres acteurs
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">LA RELATION AVEC LES AUTRES ACTEURS</div>', '', 34, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La relation avec l’encadrement est facile, on a une bonne collaboration</div>', '', 35, 1, 31, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La relation avec mes collègues est facile, il y a un bon esprit d’équipe</div>', '', 36, 1, 32, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La relation avec les autres services est facile, il y a une bonne coopération</div>', '', 37, 1, 33, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Le service Ressources Humaines sait être un soutien et nous accompagner si besoin</div>', '', 38, 1, 34, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La relation avec la Direction de l’entreprise est bonne</div>', '', 39, 1, 35, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">D’une façon générale, j’ai le sentiment de travailler dans un environnement bienveillant où je peux trouver du soutien si nécessaire</div>', '', 40, 1, 36, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Mes relations au travail sont agréables : il n’y a pas de mésententes persistantes, de moqueries, d’oppressions...</div>', '', 41, 1, 37, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je travaille dans une entreprise où les gens sont bienveillants : il n’y a pas d’intimidations, de discriminations, de manque de respect, harcèlement, violence verbale, violence physique...</div>', '', 42, 1, 38, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis plutôt heureux de retrouver chaque jour la plupart des personnes avec qui je suis amené à travailler</div>', '', 43, 1, 39, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Le rythme de travail permet d’avoir quelques pauses où je peux échanger avec les autres, parler de choses et d’autres</div>', '', 44, 1, 40, 1, 0),

-- CHAPTER: 5. La reconnaissance
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">LA RECONNAISSANCE</div>', '', 45, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">L’encadrement connaît bien la réalité de mon travail dont ses difficultés</div>', '', 46, 1, 41, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens reconnu par l’encadrement</div>', '', 47, 1, 42, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La Direction connait bien la réalité de mon travail dont ses difficultés</div>', '', 48, 1, 43, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens reconnu par l’entreprise et sa Direction</div>', '', 49, 1, 44, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Les personnes avec qui je suis amené à travailler savent ce que je fais et reconnaissent mon travail</div>', '', 50, 1, 45, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Les résultats sont importants mais j’ai le sentiment que l’encadrement ne regarde pas que ça. Il prend aussi en compte le travail fourni, l’implication et les efforts réalisés</div>', '', 51, 1, 46, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai le sentiment de faire un travail utile</div>', '', 52, 1, 47, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Généralement j’ai le sentiment d’avoir réalisé le travail attendu et je suis satisfait de ce que j’ai fait</div>', '', 53, 1, 48, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Généralement j’ai le sentiment que l’encadrement est satisfait du travail que j’ai effectué</div>', '', 54, 1, 49, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Globalement l’entreprise reconnaît de façon juste et équitable le travail et l’investissement des personnes</div>', '', 55, 1, 50, 1, 0),

-- CHAPTER: 6. La récupération
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">LA RÉCUPÉRATION</div>', '', 56, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’arrive à trouver un bon équilibre entre ma vie professionnelle et ma vie personnelle</div>', '', 57, 1, 51, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai des moments, même courts, de décompression, de respiration dans mes journées de travail</div>', '', 58, 1, 52, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai des moments où je peux prendre du recul, faire le point et voir où j’en suis, voir ce qui doit évoluer dans mon travail</div>', '', 59, 1, 53, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai des moments où je peux échanger, discuter, faire part de mes questions, doutes, difficultés, réfléchir avec mon entourage professionnel</div>', '', 60, 1, 54, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Seul ou avec d’autres (collègues, encadrement), j’ai des moments où je peux apprécier le travail que j’ai réalisé et me sentir satisfait</div>', '', 61, 1, 55, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je peux réaliser ce qu’on attend de moi en respectant mes horaires de travail, en prenant le temps de manger, en prenant mes pauses dans la journée</div>', '', 62, 1, 56, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’arrive à faire face à ma charge de travail</div>', '', 63, 1, 57, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J’ai plutôt le sentiment d’avoir les moyens et les capacités de faire le travail qui m’est demandé</div>', '', 64, 1, 58, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens serein dans mon travail actuel</div>', '', 65, 1, 59, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je peux profiter pleinement de mes moments de récupération car j’ai l’esprit libre</div>', '', 66, 1, 60, 1, 0),

-- CHAPTER: 7. La réciprocité
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">LA RÉCIPROCITÉ</div>', '', 67, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Aujourd’hui dans cette entreprise, on a plus intérêt à être acteur, à s’impliquer, à se mobiliser qu’à être passif</div>', '', 68, 1, 61, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">L’état d’esprit de l’entreprise est bien dans le « gagnant/gagnant », « perdant/perdant »</div>', '', 69, 1, 62, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Une personne qui rencontre des problèmes dans son travail et qui ne contribue pas à les résoudre a gros à perdre</div>', '', 70, 1, 63, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Une personne qui ne prend pas en compte les contraintes et les besoins des personnes, des services avec lesquels il travaille a gros à perdre</div>', '', 71, 1, 64, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Dans cette entreprise le contrat est clair : chacun sait ce dont il peut bénéficier en contre partie de son travail, de son implication</div>', '', 72, 1, 65, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">De même, chacun sait ce qu’il risque s’il ne joue pas le jeu</div>', '', 73, 1, 66, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Dans cette entreprise, chacun a plus intérêt à avoir un esprit d’équipe et collaboratif avec tout le monde qu’à être individualiste</div>', '', 74, 1, 67, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La plupart des personnes et des services sont liés par des objectifs communs qui nous obligent à dépasser nos intérêts individuels</div>', '', 75, 1, 68, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Les personnes individualistes sont pénalisées ou sanctionnées</div>', '', 76, 1, 69, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">L’entreprise fait ce qu’il faut pour que ceux qui « jouent le jeu » maintiennent leur implication et aient envie de rester</div>', '', 77, 1, 70, 1, 0),

-- CHAPTER: 8. Mes ressentis, mon état général
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">MES RESSENTIS, MON ÉTAT GÉNÉRAL</div>', '', 78, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">La plupart du temps je me sens plutôt détendu, serein (je ne ressens pas de tensions musculaires, de crispation, de nervosité...)</div>', '', 79, 1, 71, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens plutôt en pleine santé, en pleine forme : je n’ai aucun souci de santé (maux de tête fréquents, maux de dos, troubles du sommeil, troubles digestifs, troubles respiratoires, fatigue fréquente...)</div>', '', 80, 1, 72, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Globalement je me sens plutôt heureux, de bonne humeur (je ne me sens ni inquiet, ni triste, ni de mauvaise humeur, ni frustré...)</div>', '', 81, 1, 73, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je trouve ma vie agréable</div>', '', 82, 1, 74, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens plutôt motivé, acteur de ma vie (je ne ressens ni manque d’envie, ni difficulté à me mettre à la tâche, ni manque d’enthousiasme...)</div>', '', 83, 1, 75, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens bien dans ma peau, dans ma façon d’être (je ne me sens ni en manque de confiance, ni maladroit, ni mal à l’aise, ni de mauvaise humeur...)</div>', '', 84, 1, 76, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens en pleine possession de mes moyens intellectuels (je ne ressens ni fatigue, ni usure, ni confusions, ni troubles de mémoire ou vides, ni trop préoccupé...)</div>', '', 85, 1, 77, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens bien avec les autres et je me sens bien si je suis seul (je ne ressens ni sentiment de solitude ou d’isolement, ni une difficulté à être bien avec les autres ou des difficultés fréquentes de relation, ni des difficultés à être seul...)</div>', '', 86, 1, 78, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je vis bien mon travail (je ne suis pas surstressé, angoissé, démoralisé, en mal-être...)</div>', '', 87, 1, 79, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quand je rentre chez moi, je peux me consacrer à ma vie personnelle sans que le travail ne me pollue l’esprit</div>', '', 88, 1, 80, 1, 0),


-- CHAPTER: 9
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold"></div>', '', 89, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 90, 1, 81, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 91, 1, 82, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 92, 1, 83, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 93, 1, 84, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 94, 1, 85, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 95, 1, 86, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 96, 1, 87, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 97, 1, 88, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 98, 1, 89, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 99, 1, 90, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 100, 1, 91, 1, 0),


-- CHAPTER: 10
('BAROM', 'CHAPTER', '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold"></div>', '', 101, 0, NULL, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 102, 1, 92, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 103, 1, 93, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 104, 1, 94, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 105, 1, 95, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 106, 1, 96, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 107, 1, 97, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 108, 1, 98, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 109, 1, 99, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 110, 1, 100, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 111, 1, 101, 1, 0),
('BAROM', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS"></div>', '', 112, 1, 102, 1, 0);


INSERT INTO template_quiz_question (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre, editable, deletable)
VALUES
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis attentif(ve) à être apprécié(e) par les autres, au risque de ne pas toujours défendre mes droits et intérêts</div>', '', 1, 1, 1, 1, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je rends facilement service, sans me préoccuper de savoir si le(la) bénéficiaire aurait pu ou dû se débrouiller par lui(elle)-même</div>', '', 2, 1, 2, 1, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai l\'habitude de gérer mes priorités, de distinguer ce qui est vraiment important avant de me mettre au travail</div>', '', 3, 1, 3, 1, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'accepte sans souci de réaliser les parties les moins plaisantes de mon travail</div>', '', 4, 1, 1, 4, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Dans les discussions, quand on m\'attaque sur mes idées, je ne me braque pas et je suis prêt(e), face à des arguments fondés, à remettre en cause certains de mes points de vue</div>', '', 5, 1, 1, 5, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai du mal à accepter les erreurs. Cela m\'irrite</div>', '', 6, 1, 1, 6, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Même si je ne le montre pas, je suis facilement perturbé(e) par les critiques que les autres me font sur mes façons d\'agir</div>', '', 7, 1, 1, 7, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'aime le travail bien fait, précis et réalisé dans les délais voulus</div>', '', 8, 1, 1, 8, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai tendance à tout vouloir contrôler pour m\'assurer que le travail est fait comme je l\'entends</div>', '', 9, 1, 1, 9, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n\'hésite pas à prendre un travail pour soulager un(e) collègue débordé(e) ou fatigué(e), même si je suis moi-même saturé(e)</div>', '', 10, 1, 1, 10, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je dis clairement, sans détour, à mes collaborateurs(trices) ce que sont mes attendus</div>', '', 11, 1, 1, 11, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n\'aime pas qu\'on m\'impose quelque chose</div>', '', 12, 1, 1, 12, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'aime bien, au travail, avoir des moments où on rit avec une bonne histoire</div>', '', 13, 1, 1, 13, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai du mal avec les contraintes , j\'aime faire ce que je veux</div>', '', 14, 1, 1, 14, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis disponible pour écouter les gens quand ils semblent préoccupés ou en difficulté</div>', '', 15, 1, 1, 15, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je ne crois pas beaucoup aux recettes. Je préfère expérimenter par moi-même</div>', '', 16, 1, 1, 16, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Prendre des initiatives et/ou des responsabilités me crée généralement du stress</div>', '', 17, 1, 1, 17, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je passe plutôt bien avec les gens, quel que soit leur fonction, métier, niveau hiérarchique...</div>', '', 18, 1, 1, 18, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Suivre les règlements ne me pose pas de problème</div>', '', 19, 1, 1, 19, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai des principes, des valeurs et je cherche à les faire appliquer</div>', '', 20, 1, 1, 20, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai du mal à accepter qu\'on me dicte ce que je dois faire. J\'aime bien me débrouiller tout(e) seul(e)</div>', '', 21, 1, 1, 21, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je vois facilement le détail qui ne va pas</div>', '', 22, 1, 1, 22, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense qu\'il est préférable de considérer a priori les gens comme intelligents et honnêtes. Il sera temps de voir ensuite si cela n\'est pas tout à fait vrai</div>', '', 23, 1, 1, 23, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n\'aime pas laisser sans aide quelqu\'un en difficulté, même lorsqu\'il(elle) est censé(e) assumer</div>', '', 24, 1, 1, 24, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'aime bien aider les autres à apprendre, à progresser</div>', '', 25, 1, 1, 25, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Diriger, c\'est permettre à chacun(e) d\'utiliser au mieux son potentiel personnel</div>', '', 26, 1, 1, 26, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis à l\'aise pour mettre clairement les personnes en face de ce qui ne va pas lorsque cela se justifie</div>', '', 27, 1, 1, 27, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Il m\'arrive encore trop souvent de continuer à suivre certains principes, alors que je doute de plus en plus de leur valeur. Mais j\'ai été élevé(e) comme ça, je n\'y peux rien</div>', '', 28, 1, 1, 28, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je préfère suivre l\'avis de personnes de confiance plutôt que de risquer d\'expérimenter ma solution même si elle me semble plus pertinente</div>', '', 29, 1, 1, 29, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une forte tendance à consulter, m\'informer, confronter mes avis pour m\'éclairer, élargir ma réflexion</div>', '', 30, 1, 1, 30, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je transmets facilement mes connaissances, mon savoir-faire s\'il peut être utile</div>', '', 31, 1, 1, 31, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis plutôt généreux(se). J\'aime bien faire des cadeaux</div>', '', 32, 1, 1, 32, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je prends le temps de m\'occuper de moi et de me faire plaisir</div>', '', 33, 1, 1, 33, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai du mal à m\'affirmer, défendre mon point de vue devant des personnes qui m\'impressionnent</div>', '', 34, 1, 1, 34, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'aime bien manger et boire, et parfois je dépasse la mesure raisonnable</div>', '', 35, 1, 1, 35, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quand on me cherche, on me trouve</div>', '', 36, 1, 1, 36, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense qu\'il est important d\'être estimé(e) de son entourage et je m\'y emploie avec succès</div>', '', 37, 1, 1, 37, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je prends les problèmes des autres en considération et j\'essaye, dans la mesure du possible, de les aider à trouver une solution</div>', '', 38, 1, 1, 38, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n\'arrive pas toujours à contrôler mon impulsivité, à accepter la frustration</div>', '', 39, 1, 1, 39, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai un bon contrôle de moi-même et je suis plutôt un animal à sang froid</div>', '', 40, 1, 1, 40, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Avant de décider je m\'informe, j\'étudie les options. J\'évalue la faisabilité et les conséquences</div>', '', 41, 1, 1, 41, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis trop indulgent(e) pour les erreurs des autres</div>', '', 42, 1, 1, 42, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis attentif(ve) à ce que chacun assume ses responsabilités et je le dis clairement</div>', '', 43, 1, 1, 43, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis discret(e) et modeste, ce qui m\'amène à rester sur la réserve même là où il serait légitime que je me manifeste</div>', '', 44, 1, 1, 44, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis plutôt quelqu\'un qui fait respecter les engagements et les règles justifiées</div>', '', 45, 1, 1, 45, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quand un(e) collègue de travail me demande des informations, si je les ai je n\'hésite pas à les lui donner</div>', '', 46, 1, 1, 46, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai une bonne capacité à encourager, soutenir les gens</div>', '', 47, 1, 1, 47, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je peux être impulsif(ve), irritable, quand ça ne va pas comme je le voudrais</div>', '', 48, 1, 1, 48, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Il m\'arrive de vexer ou stresser les gens par maladresse</div>', '', 49, 1, 1, 49, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'arrive assez bien à prévoir ce qui va arriver quand je commence une action , j\'ai au moins plusieurs scénarios possibles en tête</div>', '', 50, 1, 1, 50, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis un(e) bon(ne) médiateur(trice) et j\'ai une bonne capacité pour régler efficacement des problèmes difficiles de relation entre personnes</div>', '', 51, 1, 1, 51, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je cherche le plus souvent possible à prendre du recul face aux problématiques, à objectiver mon analyse</div>', '', 52, 1, 1, 52, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je considère que ça fait partie de mon rôle d\'apporter du soutien à mes collaborateurs(trices)</div>', '', 53, 1, 1, 53, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai tendance à chercher à réconforter quelqu\'un(e) en difficulté, même s\'il(si elle) ne le demande pas</div>', '', 54, 1, 1, 54, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je préfère donner des conseils, aider les personnes en difficulté que les manager</div>', '', 55, 1, 1, 55, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quand on me demande quelque chose, j\'accepte trop facilement même si je suis en surcharge</div>', '', 56, 1, 1, 56, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je travaille bien et j\'applique les procédures prévues</div>', '', 57, 1, 1, 57, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n\'ai pas de problème avec l\'autorité en général</div>', '', 58, 1, 1, 58, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je m\'entends un peu trop souvent dire oui alors que je voulais dire non</div>', '', 59, 1, 1, 59, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai beaucoup d\'amis et nous nous voyons souvent pour partager nos joies, nos émotions et états d\'âme</div>', '', 60, 1, 1, 60, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je sais dire le mot qui détend l\'atmosphère et qui met la bonne ambiance</div>', '', 61, 1, 1, 61, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Les activités physiques ou ludiques (marche, sport, jardinage, jeux, sorties...) me plaisent beaucoup et me mettent en pleine forme</div>', '', 62, 1, 1, 62, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Si on me frustre de quelque chose, même si je ne le montre pas, je suis mécontent et il me faut du temps pour en sortir</div>', '', 63, 1, 1, 63, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis en général à l\'heure à mes rendez-vous et aux réunions et j\'évite de faire attendre les gens qui viennent me voir</div>', '', 64, 1, 1, 64, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je me sens libre dans mes choix et mes initiatives</div>', '', 65, 1, 1, 65, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Dans un partage, j\'accepte facilement la moins bonne part et je trouve que c\'est bien de savoir s\'effacer</div>', '', 66, 1, 1, 66, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Même si je ne le dis pas, j\'ai tendance à trouver que les autres sont mieux que moi</div>', '', 67, 1, 1, 67, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Voir la faiblesse ou l\'indécision des autres m\'irrite</div>', '', 68, 1, 1, 68, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Ce qui me résiste à tendance à m\'énerver</div>', '', 69, 1, 1, 69, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis estimé(e) par presque tout le monde</div>', '', 70, 1, 1, 70, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense que les faibles ont des excuses alors que les forts ont des solutions</div>', '', 71, 1, 1, 71, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je cherche généralement à comprendre le point de vue des autres, notamment quand il diffère du mien</div>', '', 72, 1, 1, 72, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Même si je ne le montre pas, je supporte mal les gens qui ne sont pas d\'accord avec moi</div>', '', 73, 1, 1, 73, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai l\'esprit très curieux, je me passionne facilement</div>', '', 74, 1, 1, 74, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis vivant(e), spontané(e), expressif(ve). On me trouve plutôt heureux(se) de vivre</div>', '', 75, 1, 1, 75, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Pour vivre en société, il faut un minimum de discipline et de règles à respecter</div>', '', 76, 1, 1, 76, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'aime discuter, convaincre, avoir raison</div>', '', 77, 1, 1, 77, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je demande à chacun(e) de se mobiliser pour respecter son contrat, tenir ses engagements</div>', '', 78, 1, 1, 78, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Les excès de vitesse doivent être sanctionnés</div>', '', 79, 1, 1, 79, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Il m\'arrive facilement de couper la parole de mon interlocuteur(trice)</div>', '', 80, 1, 1, 80, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Il m\'arrive assez souvent d\'être critique, de considérer qu\'on est entouré de gens qui manquent d\'exigence, de compétences</div>', '', 81, 1, 1, 81, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis plutôt fidèle aux gens</div>', '', 82, 1, 1, 82, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je suis exigeant(e) avec moi, avec mon entourage pour que chacun(e) assume ce qu\'il a à faire et ce, dans les règles de l\'art</div>', '', 83, 1, 1, 83, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je n\'hésite pas à intervenir pour recadrer ou mettre ceux(celles) qui m\'entourent en face de ce qui n\'est pas fait comme prévu</div>', '', 84, 1, 1, 84, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">J\'ai tendance à être trop bon(ne)</div>', '', 85, 1, 1, 85, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je pense qu\'avec de bons conseils, de la pédagogie et de la patience, on peut faire progresser tout le monde</div>', '', 86, 1, 1, 86, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quand les gens ont un problème, ils peuvent compter sur moi pour le résoudre</div>', '', 87, 1, 1, 87, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Je ne me force pas trop le tempérament et j\'arrive plutôt à réaliser ce que je désire</div>', '', 88, 1, 1, 88, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">Quand quelque chose m\'agace ça m\'est difficile de ne pas le montrer</div>', '', 89, 1, 1, 89, 0),
('PRCC', 'INPUT-RADIO', '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">En général, je sais tirer profit des critiques qui me sont faites</div>', '', 90, 1, 1, 90, 0);

CREATE TABLE quiz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(10) DEFAULT NULL,
    type VARCHAR(10) NOT NULL,
    name VARCHAR(1000) NOT NULL,
    auto_user_id INT DEFAULT NULL,
    auto_user_lastname VARCHAR(100) DEFAULT NULL,
    auto_user_firstname VARCHAR(100) DEFAULT NULL,
    auto_user_identifier VARCHAR(10) DEFAULT NULL,
    auto_user_email VARCHAR(150) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logo VARCHAR(255),
    start_date DATE,
    end_date DATE,
    reminder_date DATE,
    color_form VARCHAR(7) DEFAULT NULL,
    coef_tafv INT DEFAULT NULL,
    coef_pv INT DEFAULT NULL,
    coef_ppv INT DEFAULT NULL,
    coef_pdtv INT DEFAULT NULL,
    risque_de_sr INT DEFAULT NULL,
    risque_de_pdr INT DEFAULT NULL,
    risque_de_r INT DEFAULT NULL,
    risque_de_fr INT DEFAULT NULL,
    risque_a_sr INT DEFAULT NULL,
    risque_a_pdr INT DEFAULT NULL,
    risque_a_r INT DEFAULT NULL,
    risque_a_fr INT DEFAULT NULL,
    taux_de_sr INT DEFAULT NULL,
    taux_de_pdr INT DEFAULT NULL,
    taux_de_r INT DEFAULT NULL,
    taux_de_fr INT DEFAULT NULL,
    taux_a_sr INT DEFAULT NULL,
    taux_a_pdr INT DEFAULT NULL,
    taux_a_r INT DEFAULT NULL,
    taux_a_fr INT DEFAULT NULL,
    sexe_auto_user VARCHAR(1),
    header VARCHAR(100),
    intro TEXT,
    conclusion TEXT,
    footer VARCHAR(100),
    groupe_id INT,
    fonction_auto_user VARCHAR(100),
    deleted TINYINT(1) NOT NULL,
    anonymous TINYINT(1) NOT NULL,
    cc_p1_l1 TEXT,
    cc_p1_l2 TEXT,
    cc_p1_l3 TEXT,
    cc_p1_l4 TEXT,
    cc_p1_l5 TEXT,
    cc_p2_l1 TEXT,
    cc_p2_l2 TEXT,
    cc_p2_l3 TEXT,
    cc_p2_l4 TEXT,
    cc_p2_l5 TEXT,
    cc_p3_l1 TEXT,
    cc_p3_l2 TEXT,
    cc_p3_l3 TEXT,
    cc_p3_l4 TEXT,
    cc_p3_l5 TEXT
);

CREATE TABLE quiz_question (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               quiz_id INT NOT NULL,
                               quiz_type VARCHAR(5) NOT NULL,
                               question_type VARCHAR(20) NOT NULL,
                               label VARCHAR(2000) NOT NULL,
                               label_auto VARCHAR(2000) NOT NULL,
                               ordre INT NOT NULL,
                               response_required TINYINT(1) NOT NULL,
                               report_ordre INT,
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quiz_user (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT DEFAULT NULL,
                           user_lastname VARCHAR(100) DEFAULT NULL,
                           user_firstname VARCHAR(100) DEFAULT NULL,
                           user_identifier VARCHAR(10) DEFAULT NULL,
                           user_email VARCHAR(150) DEFAULT NULL,
                           quiz_id INT NOT NULL,
                           auto TINYINT(1) NOT NULL,
                           status VARCHAR(10) DEFAULT 'TODO',
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quiz_user_response (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    quiz_user_id INT NOT NULL,
                                    question_id INT NOT NULL,
                                    value VARCHAR(255) NOT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE email_template (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                title VARCHAR(255) NOT NULL,
                                message TEXT NOT NULL,
                                deleteable INT NOT NULL DEFAULT 1
);

CREATE TABLE quiz_criteres_barometre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    critere1_titre VARCHAR(255) NULL,
    critere1_choix1 VARCHAR(255) NULL,
    critere1_choix2 VARCHAR(255) NULL,
    critere1_choix3 VARCHAR(255) NULL,
    critere1_choix4 VARCHAR(255) NULL,
    critere1_choix5 VARCHAR(255) NULL,
    critere1_choix6 VARCHAR(255) NULL,
    critere1_choix7 VARCHAR(255) NULL,
    critere1_choix8 VARCHAR(255) NULL,
    critere1_choix9 VARCHAR(255) NULL,
    critere1_choix10 VARCHAR(255) NULL,
    critere2_titre VARCHAR(255) NULL,
    critere2_choix1 VARCHAR(255) NULL,
    critere2_choix2 VARCHAR(255) NULL,
    critere2_choix3 VARCHAR(255) NULL,
    critere2_choix4 VARCHAR(255) NULL,
    critere2_choix5 VARCHAR(255) NULL,
    critere2_choix6 VARCHAR(255) NULL,
    critere2_choix7 VARCHAR(255) NULL,
    critere2_choix8 VARCHAR(255) NULL,
    critere2_choix9 VARCHAR(255) NULL,
    critere2_choix10 VARCHAR(255) NULL,
    critere3_titre VARCHAR(255) NULL,
    critere3_choix1 VARCHAR(255) NULL,
    critere3_choix2 VARCHAR(255) NULL,
    critere3_choix3 VARCHAR(255) NULL,
    critere3_choix4 VARCHAR(255) NULL,
    critere3_choix5 VARCHAR(255) NULL,
    critere3_choix6 VARCHAR(255) NULL,
    critere3_choix7 VARCHAR(255) NULL,
    critere3_choix8 VARCHAR(255) NULL,
    critere3_choix9 VARCHAR(255) NULL,
    critere3_choix10 VARCHAR(255) NULL,
    critere4_titre VARCHAR(255) NULL,
    critere4_choix1 VARCHAR(255) NULL,
    critere4_choix2 VARCHAR(255) NULL,
    critere4_choix3 VARCHAR(255) NULL,
    critere4_choix4 VARCHAR(255) NULL,
    critere4_choix5 VARCHAR(255) NULL,
    critere4_choix6 VARCHAR(255) NULL,
    critere4_choix7 VARCHAR(255) NULL,
    critere4_choix8 VARCHAR(255) NULL,
    critere4_choix9 VARCHAR(255) NULL,
    critere4_choix10 VARCHAR(255) NULL,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id)
);

CREATE TABLE template_quiz_criteres_barometre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_critere1_titre VARCHAR(255) NULL,
    template_critere1_choix1 VARCHAR(255) NULL,
    template_critere1_choix2 VARCHAR(255) NULL,
    template_critere1_choix3 VARCHAR(255) NULL,
    template_critere1_choix4 VARCHAR(255) NULL,
    template_critere1_choix5 VARCHAR(255) NULL,
    template_critere1_choix6 VARCHAR(255) NULL,
    template_critere1_choix7 VARCHAR(255) NULL,
    template_critere1_choix8 VARCHAR(255) NULL,
    template_critere1_choix9 VARCHAR(255) NULL,
    template_critere1_choix10 VARCHAR(255) NULL,
    template_critere2_titre VARCHAR(255) NULL,
    template_critere2_choix1 VARCHAR(255) NULL,
    template_critere2_choix2 VARCHAR(255) NULL,
    template_critere2_choix3 VARCHAR(255) NULL,
    template_critere2_choix4 VARCHAR(255) NULL,
    template_critere2_choix5 VARCHAR(255) NULL,
    template_critere2_choix6 VARCHAR(255) NULL,
    template_critere2_choix7 VARCHAR(255) NULL,
    template_critere2_choix8 VARCHAR(255) NULL,
    template_critere2_choix9 VARCHAR(255) NULL,
    template_critere2_choix10 VARCHAR(255) NULL,
    template_critere3_titre VARCHAR(255) NULL,
    template_critere3_choix1 VARCHAR(255) NULL,
    template_critere3_choix2 VARCHAR(255) NULL,
    template_critere3_choix3 VARCHAR(255) NULL,
    template_critere3_choix4 VARCHAR(255) NULL,
    template_critere3_choix5 VARCHAR(255) NULL,
    template_critere3_choix6 VARCHAR(255) NULL,
    template_critere3_choix7 VARCHAR(255) NULL,
    template_critere3_choix8 VARCHAR(255) NULL,
    template_critere3_choix9 VARCHAR(255) NULL,
    template_critere3_choix10 VARCHAR(255) NULL,
    template_critere4_titre VARCHAR(255) NULL,
    template_critere4_choix1 VARCHAR(255) NULL,
    template_critere4_choix2 VARCHAR(255) NULL,
    template_critere4_choix3 VARCHAR(255) NULL,
    template_critere4_choix4 VARCHAR(255) NULL,
    template_critere4_choix5 VARCHAR(255) NULL,
    template_critere4_choix6 VARCHAR(255) NULL,
    template_critere4_choix7 VARCHAR(255) NULL,
    template_critere4_choix8 VARCHAR(255) NULL,
    template_critere4_choix9 VARCHAR(255) NULL,
    template_critere4_choix10 VARCHAR(255) NULL
);

    CREATE TABLE response_quiz_criteres_barometre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_criteres_barometre_id INT NOT NULL,
    quiz_user_id INT NOT NULL,
    response_critere1 VARCHAR(255) DEFAULT NULL,
    response_critere2 VARCHAR(255) DEFAULT NULL,
    response_critere3 VARCHAR(255) DEFAULT NULL,
    response_critere4 VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (quiz_criteres_barometre_id) REFERENCES quiz_criteres_barometre(id) ON DELETE CASCADE
);
    CREATE TABLE template_quiz_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_type VARCHAR(5) NOT NULL,
    color_form VARCHAR(50) NOT NULL,
    header TEXT NOT NULL,
    intro TEXT NOT NULL,
    conclusion TEXT NOT NULL,
    footer TEXT NOT NULL,
    cc_p1_l1 TEXT NOT NULL,
    cc_p1_l2 TEXT NOT NULL,
    cc_p1_l3 TEXT NOT NULL,
    cc_p1_l4 TEXT NOT NULL,
    cc_p1_l5 TEXT NOT NULL,
    cc_p2_l1 TEXT NOT NULL,
    cc_p2_l2 TEXT NOT NULL,
    cc_p2_l3 TEXT NOT NULL,
    cc_p2_l4 TEXT NOT NULL,
    cc_p2_l5 TEXT NOT NULL,
    cc_p3_l1 TEXT NOT NULL,
    cc_p3_l2 TEXT NOT NULL,
    cc_p3_l3 TEXT NOT NULL,
    cc_p3_l4 TEXT NOT NULL,
    cc_p3_l5 TEXT NOT NULL
);

-- Insertion des données par défaut pour le quiz 360
INSERT INTO template_quiz_options (
    quiz_type, color_form, header, intro, conclusion, footer,
    cc_p1_l1, cc_p1_l2, cc_p1_l3, cc_p1_l4, cc_p1_l5,
    cc_p2_l1, cc_p2_l2, cc_p2_l3, cc_p2_l4, cc_p2_l5,
    cc_p3_l1, cc_p3_l2, cc_p3_l3, cc_p3_l4, cc_p3_l5
) VALUES (
    '360', '#696252', '', '', '', '',
    'Vous allez répondre à un questionnaire 360 social qui vise à recueillir votre perception de votre vie au travail.',
    'Ce baromètre se présente sous forme d’un questionnaire en x parties, sur différents thèmes (votre travail quotidien, vos relations avec les autres, la reconnaissance, la « récupération »…).',
    'Votre participation, très importante, nous permettra d’identifier ensemble des leviers d’actions pour améliorer la qualité de vie au travail, et la performance de l’entreprise.',
    '', '',
    'Si possible, ne soyez pas dérangé pendant que vous répondez aux questions. Vous avez besoin d’environ 20 minutes.',
    'L’attribution aléatoire des codes d’identification et la remise sous enveloppe nous permet de garantir l’anonymat de vos réponses.',
    '', '', '',
    'Si vous avez des questions ou si vous rencontrez des difficultés pour répondre au questionnaire, n’hésitez pas à interroger CONTACT_NOM.',
    'Nous vous remercions de votre participation,',
    '[CONTACT_NOM], consultante Relais Managers',
    '', ''
);

-- Insertion des données par défaut pour le quiz BAROM
INSERT INTO template_quiz_options (
    quiz_type, color_form, header, intro, conclusion, footer,
    cc_p1_l1, cc_p1_l2, cc_p1_l3, cc_p1_l4, cc_p1_l5,
    cc_p2_l1, cc_p2_l2, cc_p2_l3, cc_p2_l4, cc_p2_l5,
    cc_p3_l1, cc_p3_l2, cc_p3_l3, cc_p3_l4, cc_p3_l5
) VALUES (
    'BAROM', '#696252', '', '', '', '',
    'Vous allez répondre à un baromètre social qui vise à recueillir votre perception de votre vie au travail.',
    'Ce baromètre se présente sous forme d’un questionnaire en huit parties, sur différents thèmes (votre travail quotidien, vos relations avec les autres, la reconnaissance, la « récupération »…).',
    'Votre participation, très importante, nous permettra d’identifier ensemble des leviers d’actions pour améliorer la qualité de vie au travail, et la performance de l’entreprise.',
    '', '',
    'Si possible, ne soyez pas dérangé pendant que vous répondez aux questions. Vous avez besoin d’environ 20 minutes.',
    'L’attribution aléatoire des codes d’identification et la remise sous enveloppe nous permet de garantir l’anonymat de vos réponses.',
    '', '', '',
    'Si vous avez des questions ou si vous rencontrez des difficultés pour répondre au questionnaire, n’hésitez pas à interroger PARAM GLOBAL NOM CONTACT.',
    'Nous vous remercions de votre participation,',
    '[PARAM GLOBAL NOM CONTACT], consultante Relais Managers',
    '', ''
);

CREATE TABLE parameters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255),
    value VARCHAR(255),
    label VARCHAR(255)
);
INSERT INTO parameters (code, value, label)
VALUES ('[CONTACT_NOM]', 'Nathalie ROBERT', 'Nom Prénom du contact relais manager (indiqué par exemple dans les courriers de convocation)');

INSERT INTO parameters (code, value, label)
VALUES ('[CONTACT_TELEPHONE]', '04 78 60 42 73', 'Numéro de téléphone du contact relais manager (indiqué par exemple dans les courriers de convocation)');

CREATE TABLE template_prcc_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(255) NOT NULL
);


INSERT INTO template_prcc_category (label) VALUES
('Parent NORMATIF'),
('Parent NORMATIF EXCESSIF (PERSECUTEUR)'),
('Parent NORMATIF PEU DEVELOPPE'),
('Parent NOURRICIER (DONNANT)'),
('Parent NOURRICIER EXCESSIF (SAUVEUR)'),
('Parent NOURRICIER PEU DEVELOPPE'),
('ADULTE'),
('ADULTE PEU DEVELOPPE'),
('Enfant LIBRE'),
('Enfant LIBRE EXCESSIF (REBELLE)'),
('Enfant LIBRE PEU DEVELOPPE'),
('Enfant ADAPTE'),
('Enfant ADAPTE EXCESSIF (SOUMIS ou SUR-ADAPTE)'),
('Enfant ADAPTE PEU DEVELOPPE');


INSERT INTO template_quiz_criteres_barometre (
    template_critere1_titre, template_critere1_choix1, template_critere1_choix2, template_critere1_choix3,
    template_critere2_titre, template_critere2_choix1, template_critere2_choix2, template_critere2_choix3, template_critere2_choix4, template_critere2_choix5, template_critere2_choix6, template_critere2_choix7,
    template_critere3_titre, template_critere3_choix1, template_critere3_choix2, template_critere3_choix3
) VALUES (
    'ANCIENNETÉ', 'Moins de 5 ans', 'Entre 5 et 20 ans', 'Plus de 20 ans',
    'FONCTION', 'Commercial', 'Enduction-mélange', 'Finance-RH-sécurité', 'Labos R&D-Contrôles-PTL Service-Achats', 'Maintenance-Travaux neufs', 'Piquage-Visite-Découpe', 'Supply chain (ADV-Planning-Appros-Expéditions-préparation commandes)',
    'CSP', 'Cadre', 'Employé-Technicien-AM', 'Ouvrier'
    );

INSERT INTO email_template (title, message, deleteable)
VALUES
    ('Modèle de base',
     'Bonjour [PRENOM] [NOM],<br><br>
     Vous êtes invité(e) à remplir le questionnaire suivant : <br><br>
     <a href="[URL]">[URL]</a><br><br>
     Ce lien vous est réservé, ne le transmettez pas.',
     0);
INSERT INTO email_template (title, message, deleteable)
VALUES
    ('Template de Rappel de Participation',
     'Bonjour [PRENOM] [NOM],<br><br>
     Nous vous rappelons que le questionnaire intitulé "<strong>[TITRE]</strong>" auquel vous êtes invité(e) à participer est toujours ouvert.<br><br>
     La date limite pour y répondre est le <strong>[DATE_FIN]</strong>. Vous pouvez accéder à l\'enquête en cliquant sur le lien suivant : <a href="[URL]">[URL]</a><br><br>
    Ne manquez pas l\'opportunité de donner votre avis !<br><br>
    Si vous avez des questions, n\'hésitez pas à nous contacter.<br><br>
    Cordialement,<br>
     L\'équipe de Relais Managers',
     1);

-- Insertion des données par défaut pour le quiz BAROM
INSERT INTO template_quiz_options (
    quiz_type, color_form, header, intro, conclusion, footer,
    cc_p1_l1, cc_p1_l2, cc_p1_l3, cc_p1_l4, cc_p1_l5,
    cc_p2_l1, cc_p2_l2, cc_p2_l3, cc_p2_l4, cc_p2_l5,
    cc_p3_l1, cc_p3_l2, cc_p3_l3, cc_p3_l4, cc_p3_l5
) VALUES (
    'PRCC', '#696252', '', '', '', '',
    'Vous allez répondre à un baromètre social qui vise à recueillir votre perception de votre vie au travail.',
    'Ce baromètre se présente sous forme d’un questionnaire en huit parties, sur différents thèmes (votre travail quotidien, vos relations avec les autres, la reconnaissance, la « récupération »…).',
    'Votre participation, très importante, nous permettra d’identifier ensemble des leviers d’actions pour améliorer la qualité de vie au travail, et la performance de l’entreprise.',
    '', '',
    'Si possible, ne soyez pas dérangé pendant que vous répondez aux questions. Vous avez besoin d’environ 20 minutes.',
    'L’attribution aléatoire des codes d’identification et la remise sous enveloppe nous permet de garantir l’anonymat de vos réponses.',
    '', '', '',
    'Si vous avez des questions ou si vous rencontrez des difficultés pour répondre au questionnaire, n’hésitez pas à interroger PARAM GLOBAL NOM CONTACT.',
    'Nous vous remercions de votre participation,',
    '[PARAM GLOBAL NOM CONTACT], consultante Relais Managers',
    '', ''
);

ALTER TABLE template_quiz_question ADD COLUMN  prcc_category_id INT NOT NULL DEFAULT 0;

ALTER TABLE quiz_question ADD COLUMN  prcc_category_id INT NOT NULL DEFAULT 0;

UPDATE template_quiz_question set prcc_category_id = 1 WHERE quiz_type = 'PRCC' AND ordre IN (8, 11, 27, 43, 45, 76, 78, 79, 83, 84);
UPDATE template_quiz_question set prcc_category_id = 4 WHERE quiz_type = 'PRCC' AND ordre IN (15, 23, 25, 26, 30, 32, 38, 46, 47, 53);
UPDATE template_quiz_question set prcc_category_id = 7 WHERE quiz_type = 'PRCC' AND ordre IN (3, 5, 16, 31, 40, 41, 50, 51, 52, 72);
UPDATE template_quiz_question set prcc_category_id = 9 WHERE quiz_type = 'PRCC' AND ordre IN (13, 33, 35, 60, 61, 62, 65, 74, 75, 88);
UPDATE template_quiz_question set prcc_category_id = 12 WHERE quiz_type = 'PRCC' AND ordre IN (4, 18, 19, 37, 57, 58, 64, 70, 82, 90);
UPDATE template_quiz_question set prcc_category_id = 2 WHERE quiz_type = 'PRCC' AND ordre IN (6, 9, 20, 22, 49, 68, 71, 77, 80, 81);
UPDATE template_quiz_question set prcc_category_id = 5 WHERE quiz_type = 'PRCC' AND ordre IN (2, 10, 24, 42, 54, 55, 66, 85, 86, 87);
UPDATE template_quiz_question set prcc_category_id = 10 WHERE quiz_type = 'PRCC' AND ordre IN (12, 14, 21, 36, 39, 48, 63, 69, 73, 89);
UPDATE template_quiz_question set prcc_category_id = 13 WHERE quiz_type = 'PRCC' AND ordre IN (1, 7, 17, 28, 29, 34, 44, 56, 59, 67);

UPDATE quiz_question set prcc_category_id = 1 WHERE quiz_type = 'PRCC' AND ordre IN (8, 11, 27, 43, 45, 76, 78, 79, 83, 84);
UPDATE quiz_question set prcc_category_id = 4 WHERE quiz_type = 'PRCC' AND ordre IN (15, 23, 25, 26, 30, 32, 38, 46, 47, 53);
UPDATE quiz_question set prcc_category_id = 7 WHERE quiz_type = 'PRCC' AND ordre IN (3, 5, 16, 31, 40, 41, 50, 51, 52, 72);
UPDATE quiz_question set prcc_category_id = 9 WHERE quiz_type = 'PRCC' AND ordre IN (13, 33, 35, 60, 61, 62, 65, 74, 75, 88);
UPDATE quiz_question set prcc_category_id = 12 WHERE quiz_type = 'PRCC' AND ordre IN (4, 18, 19, 37, 57, 58, 64, 70, 82, 90);
UPDATE quiz_question set prcc_category_id = 2 WHERE quiz_type = 'PRCC' AND ordre IN (6, 9, 20, 22, 49, 68, 71, 77, 80, 81);
UPDATE quiz_question set prcc_category_id = 5 WHERE quiz_type = 'PRCC' AND ordre IN (2, 10, 24, 42, 54, 55, 66, 85, 86, 87);
UPDATE quiz_question set prcc_category_id = 10 WHERE quiz_type = 'PRCC' AND ordre IN (12, 14, 21, 36, 39, 48, 63, 69, 73, 89);
UPDATE quiz_question set prcc_category_id = 13 WHERE quiz_type = 'PRCC' AND ordre IN (1, 7, 17, 28, 29, 34, 44, 56, 59, 67);

ALTER TABLE template_prcc_category ADD COLUMN  label_short VARCHAR(255) NOT NULL;

UPDATE template_prcc_category SET label_short = 'Parent normatif' WHERE id = 1;
UPDATE template_prcc_category SET label_short = 'Parent normatif excessif' WHERE id = 2;
UPDATE template_prcc_category SET label_short = '' WHERE id = 3;
UPDATE template_prcc_category SET label_short = 'Parent nourricier' WHERE id = 4;
UPDATE template_prcc_category SET label_short = 'Parent nourricier excessif' WHERE id = 5;
UPDATE template_prcc_category SET label_short = '' WHERE id = 6;
UPDATE template_prcc_category SET label_short = 'Adulte' WHERE id = 7;
UPDATE template_prcc_category SET label_short = '' WHERE id = 8;
UPDATE template_prcc_category SET label_short = 'Enfant libre' WHERE id = 9;
UPDATE template_prcc_category SET label_short = 'Enfant libre excessif' WHERE id = 10;
UPDATE template_prcc_category SET label_short = '' WHERE id = 11;
UPDATE template_prcc_category SET label_short = 'Enfant adapté' WHERE id = 12;
UPDATE template_prcc_category SET label_short = 'Enfant adapté excessif' WHERE id = 13;
UPDATE template_prcc_category SET label_short = '' WHERE id = 14;

DELETE FROM template_prcc_category WHERE id IN (3,6,8,11,14);

CREATE TABLE quiz_report_barometre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    C1Q1 VARCHAR(5) NULL,
    C1Q2 VARCHAR(5) NULL,
    C1Q3 VARCHAR(5) NULL,
    C1Q4 VARCHAR(5) NULL,
    C1Q5 VARCHAR(5) NULL,
    C1Q6 VARCHAR(5) NULL,
    C1Q7 VARCHAR(5) NULL,
    C1Q8 VARCHAR(5) NULL,
    C1Q9 VARCHAR(5) NULL,
    C1Q10 VARCHAR(5) NULL,
    C2Q1 VARCHAR(5) NULL,
    C2Q2 VARCHAR(5) NULL,
    C2Q3 VARCHAR(5) NULL,
    C2Q4 VARCHAR(5) NULL,
    C2Q5 VARCHAR(5) NULL,
    C2Q6 VARCHAR(5) NULL,
    C2Q7 VARCHAR(5) NULL,
    C2Q8 VARCHAR(5) NULL,
    C2Q9 VARCHAR(5) NULL,
    C2Q10 VARCHAR(5) NULL,
    C3Q1 VARCHAR(5) NULL,
    C3Q2 VARCHAR(5) NULL,
    C3Q3 VARCHAR(5) NULL,
    C3Q4 VARCHAR(5) NULL,
    C3Q5 VARCHAR(5) NULL,
    C3Q6 VARCHAR(5) NULL,
    C3Q7 VARCHAR(5) NULL,
    C3Q8 VARCHAR(5) NULL,
    C3Q9 VARCHAR(5) NULL,
    C3Q10 VARCHAR(5) NULL,
    C4Q1 VARCHAR(5) NULL,
    C4Q2 VARCHAR(5) NULL,
    C4Q3 VARCHAR(5) NULL,
    C4Q4 VARCHAR(5) NULL,
    C4Q5 VARCHAR(5) NULL,
    C4Q6 VARCHAR(5) NULL,
    C4Q7 VARCHAR(5) NULL,
    C4Q8 VARCHAR(5) NULL,
    C4Q9 VARCHAR(5) NULL,
    C4Q10 VARCHAR(5) NULL,
    C5Q1 VARCHAR(5) NULL,
    C5Q2 VARCHAR(5) NULL,
    C5Q3 VARCHAR(5) NULL,
    C5Q4 VARCHAR(5) NULL,
    C5Q5 VARCHAR(5) NULL,
    C5Q6 VARCHAR(5) NULL,
    C5Q7 VARCHAR(5) NULL,
    C5Q8 VARCHAR(5) NULL,
    C5Q9 VARCHAR(5) NULL,
    C5Q10 VARCHAR(5) NULL,
    C6Q1 VARCHAR(5) NULL,
    C6Q2 VARCHAR(5) NULL,
    C6Q3 VARCHAR(5) NULL,
    C6Q4 VARCHAR(5) NULL,
    C6Q5 VARCHAR(5) NULL,
    C6Q6 VARCHAR(5) NULL,
    C6Q7 VARCHAR(5) NULL,
    C6Q8 VARCHAR(5) NULL,
    C6Q9 VARCHAR(5) NULL,
    C6Q10 VARCHAR(5) NULL,
    C7Q1 VARCHAR(5) NULL,
    C7Q2 VARCHAR(5) NULL,
    C7Q3 VARCHAR(5) NULL,
    C7Q4 VARCHAR(5) NULL,
    C7Q5 VARCHAR(5) NULL,
    C7Q6 VARCHAR(5) NULL,
    C7Q7 VARCHAR(5) NULL,
    C7Q8 VARCHAR(5) NULL,
    C7Q9 VARCHAR(5) NULL,
    C7Q10 VARCHAR(5) NULL,
    C8Q1 VARCHAR(5) NULL,
    C8Q2 VARCHAR(5) NULL,
    C8Q3 VARCHAR(5) NULL,
    C8Q4 VARCHAR(5) NULL,
    C8Q5 VARCHAR(5) NULL,
    C8Q6 VARCHAR(5) NULL,
    C8Q7 VARCHAR(5) NULL,
    C8Q8 VARCHAR(5) NULL,
    C8Q9 VARCHAR(5) NULL,
    C8Q10 VARCHAR(5) NULL,
    C9Q1 VARCHAR(5) NULL,
    C9Q2 VARCHAR(5) NULL,
    C9Q3 VARCHAR(5) NULL,
    C9Q4 VARCHAR(5) NULL,
    C9Q5 VARCHAR(5) NULL,
    C9Q6 VARCHAR(5) NULL,
    C9Q7 VARCHAR(5) NULL,
    C9Q8 VARCHAR(5) NULL,
    C9Q9 VARCHAR(5) NULL,
    C9Q10 VARCHAR(5) NULL,
    C10Q1 VARCHAR(5) NULL,
    C10Q2 VARCHAR(5) NULL,
    C10Q3 VARCHAR(5) NULL,
    C10Q4 VARCHAR(5) NULL,
    C10Q5 VARCHAR(5) NULL,
    C10Q6 VARCHAR(5) NULL,
    C10Q7 VARCHAR(5) NULL,
    C10Q8 VARCHAR(5) NULL,
    C10Q9 VARCHAR(5) NULL,
    C10Q10 VARCHAR(5) NULL,
    C1C1_coef VARCHAR(5) NULL,
    C1C2_coef VARCHAR(5) NULL,
    C1C3_coef VARCHAR(5) NULL,
    C1C4_coef VARCHAR(5) NULL,
    C2C1_coef VARCHAR(5) NULL,
    C2C2_coef VARCHAR(5) NULL,
    C2C3_coef VARCHAR(5) NULL,
    C2C4_coef VARCHAR(5) NULL,
    C3C1_coef VARCHAR(5) NULL,
    C3C2_coef VARCHAR(5) NULL,
    C3C3_coef VARCHAR(5) NULL,
    C3C4_coef VARCHAR(5) NULL,
    C4C1_coef VARCHAR(5) NULL,
    C4C2_coef VARCHAR(5) NULL,
    C4C3_coef VARCHAR(5) NULL,
    C4C4_coef VARCHAR(5) NULL,
    C5C1_coef VARCHAR(5) NULL,
    C5C2_coef VARCHAR(5) NULL,
    C5C3_coef VARCHAR(5) NULL,
    C5C4_coef VARCHAR(5) NULL,
    C6C1_coef VARCHAR(5) NULL,
    C6C2_coef VARCHAR(5) NULL,
    C6C3_coef VARCHAR(5) NULL,
    C6C4_coef VARCHAR(5) NULL,
    C7C1_coef VARCHAR(5) NULL,
    C7C2_coef VARCHAR(5) NULL,
    C7C3_coef VARCHAR(5) NULL,
    C7C4_coef VARCHAR(5) NULL,
    C8C1_coef VARCHAR(5) NULL,
    C8C2_coef VARCHAR(5) NULL,
    C8C3_coef VARCHAR(5) NULL,
    C8C4_coef VARCHAR(5) NULL,
    C9C1_coef VARCHAR(5) NULL,
    C9C2_coef VARCHAR(5) NULL,
    C9C3_coef VARCHAR(5) NULL,
    C9C4_coef VARCHAR(5) NULL,
    C10C1_coef VARCHAR(5) NULL,
    C10C2_coef VARCHAR(5) NULL,
    C10C3_coef VARCHAR(5) NULL,
    C10C4_coef VARCHAR(5) NULL,
	C1C1_risque VARCHAR(5) NULL,
    C1C2_risque VARCHAR(5) NULL,
    C1C3_risque VARCHAR(5) NULL,
    C1C4_risque VARCHAR(5) NULL,
	C2C1_risque VARCHAR(5) NULL,
    C2C2_risque VARCHAR(5) NULL,
    C2C3_risque VARCHAR(5) NULL,
    C2C4_risque VARCHAR(5) NULL,
	C3C1_risque VARCHAR(5) NULL,
    C3C2_risque VARCHAR(5) NULL,
    C3C3_risque VARCHAR(5) NULL,
    C3C4_risque VARCHAR(5) NULL,
	C4C1_risque VARCHAR(5) NULL,
    C4C2_risque VARCHAR(5) NULL,
    C4C3_risque VARCHAR(5) NULL,
    C4C4_risque VARCHAR(5) NULL,
	C5C1_risque VARCHAR(5) NULL,
    C5C2_risque VARCHAR(5) NULL,
    C5C3_risque VARCHAR(5) NULL,
    C5C4_risque VARCHAR(5) NULL,
	C6C1_risque VARCHAR(5) NULL,
    C6C2_risque VARCHAR(5) NULL,
    C6C3_risque VARCHAR(5) NULL,
    C6C4_risque VARCHAR(5) NULL,
	C7C1_risque VARCHAR(5) NULL,
    C7C2_risque VARCHAR(5) NULL,
    C7C3_risque VARCHAR(5) NULL,
    C7C4_risque VARCHAR(5) NULL,
	C8C1_risque VARCHAR(5) NULL,
    C8C2_risque VARCHAR(5) NULL,
    C8C3_risque VARCHAR(5) NULL,
    C8C4_risque VARCHAR(5) NULL,
	C9C1_risque VARCHAR(5) NULL,
    C9C2_risque VARCHAR(5) NULL,
    C9C3_risque VARCHAR(5) NULL,
    C9C4_risque VARCHAR(5) NULL,
	C10C1_risque VARCHAR(5) NULL,
    C10C2_risque VARCHAR(5) NULL,
    C10C3_risque VARCHAR(5) NULL,
    C10C4_risque VARCHAR(5) NULL,
    C1_expo VARCHAR(5) NULL,
    C2_expo VARCHAR(5) NULL,
    C3_expo VARCHAR(5) NULL,
    C4_expo VARCHAR(5) NULL,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id)
);