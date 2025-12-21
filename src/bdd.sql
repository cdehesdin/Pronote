-- Clément DEHESDIN
-- Requête SQL de la base de données du projet `pronote`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


--
-- Table `AppClasse`
--

CREATE TABLE `AppClasse` (
  `idAppClasse` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idEnseignement` int(11) NOT NULL,
  `app` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `AppClasse` (`idAppClasse`, `idEnseignement`, `app`) VALUES
(1, 1, NULL),
(2, 2, NULL),
(3, 3, NULL),
(4, 4, NULL),
(5, 5, NULL),
(6, 6, NULL),
(7, 7, NULL),
(8, 8, NULL),
(9, 9, NULL),
(10, 10, NULL),
(11, 11, NULL),
(12, 12, NULL),
(13, 13, NULL),
(14, 14, NULL),
(15, 15, NULL),
(16, 16, NULL),
(17, 17, NULL),
(18, 18, NULL),
(19, 19, NULL),
(20, 20, NULL),
(21, 21, NULL),
(22, 22, NULL),
(23, 23, NULL),
(24, 24, NULL),
(25, 25, NULL),
(26, 26, NULL),
(27, 27, NULL),
(28, 28, NULL),
(29, 29, NULL),
(30, 30, NULL),
(31, 31, NULL),
(32, 32, NULL);


--
-- Table `AppEleve`
--

CREATE TABLE `AppEleve` (
  `idAppEleve` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idEnseignement` int(11) NOT NULL,
  `idEleve` int(11) NOT NULL,
  `app` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `AppEleve` (`idAppEleve`, `idEnseignement`, `idEleve`, `app`) VALUES
(1, 1, 1, NULL),
(2, 24, 1, NULL),
(3, 31, 1, NULL),
(4, 17, 1, NULL),
(5, 14, 1, NULL),
(6, 12, 1, NULL),
(7, 8, 1, NULL),
(8, 27, 1, NULL),
(9, 22, 2, NULL),
(10, 3, 2, NULL),
(11, 30, 2, NULL),
(12, 19, 2, NULL),
(13, 7, 2, NULL),
(14, 13, 2, NULL),
(15, 9, 2, NULL),
(16, 26, 2, NULL),
(17, 4, 3, NULL),
(18, 18, 3, NULL),
(19, 16, 3, NULL),
(20, 28, 3, NULL),
(21, 23, 3, NULL),
(22, 6, 3, NULL),
(23, 11, 3, NULL),
(24, 29, 3, NULL),
(25, 1, 4, NULL),
(26, 24, 4, NULL),
(27, 31, 4, NULL),
(28, 17, 4, NULL),
(29, 14, 4, NULL),
(30, 12, 4, NULL),
(31, 8, 4, NULL),
(32, 27, 4, NULL),
(33, 4, 5, NULL),
(34, 18, 5, NULL),
(35, 16, 5, NULL),
(36, 28, 5, NULL),
(37, 23, 5, NULL),
(38, 6, 5, NULL),
(39, 11, 5, NULL),
(40, 29, 5, NULL),
(41, 32, 6, NULL),
(42, 21, 6, NULL),
(43, 10, 6, NULL),
(44, 20, 6, NULL),
(45, 15, 6, NULL),
(46, 5, 6, NULL),
(47, 25, 6, NULL),
(48, 2, 6, NULL),
(49, 32, 7, NULL),
(50, 21, 7, NULL),
(51, 10, 7, NULL),
(52, 20, 7, NULL),
(53, 15, 7, NULL),
(54, 5, 7, NULL),
(55, 25, 7, NULL),
(56, 2, 7, NULL),
(57, 22, 8, NULL),
(58, 3, 8, NULL),
(59, 30, 8, NULL),
(60, 19, 8, NULL),
(61, 7, 8, NULL),
(62, 13, 8, NULL),
(63, 9, 8, NULL),
(64, 26, 8, NULL);


--
-- Structure de la table `Classe`
--

CREATE TABLE `Classe` (
  `idClasse` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `PPID` int(11) NOT NULL,
  `appreciationPP` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Classe` (`idClasse`, `nom`, `PPID`, `appreciationPP`) VALUES
(1, '3A', 4, NULL),
(2, '3B', 8, NULL),
(3, '4A', 12, NULL),
(4, '4B', 10, NULL);


--
-- Table `Controles`
--

CREATE TABLE `Controles` (
  `idControle` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idEnseignement` int(11) NOT NULL,
  `nomControle` varchar(255) NOT NULL,
  `coefficient` float NOT NULL,
  `dates` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Controles` (`idControle`, `idEnseignement`, `nomControle`, `coefficient`, `dates`) VALUES
(1, 1, 'Évaluation sur les suites numériques', 1, '2023-05-23'),
(2, 8, 'Contrôle de grammaire', 2, '2023-07-26'),
(3, 17, 'DS sur les ondes', 1, '2024-11-02'),
(4, 3, 'Test4', 1, '2023-07-23'),
(5, 4, 'Test5', 1, '2024-12-13'),
(6, 4, 'Test6', 1, '2023-02-22'),
(7, 1, 'Évaluation sur les fonctions quadratiques', 2, '2024-02-09'),
(8, 2, 'Évaluation sur les fonctions usuelles', 1, '2024-05-02'),
(9, 14, 'Test 1', 1, '2024-05-07'),
(10, 17, 'Test 2', 3, '2024-05-11'),
(11, 24, 'Test 3', 4, '2024-05-24'),
(12, 31, 'Test 4', 4, '2024-05-03'),
(13, 27, 'Test 5', 2, '2024-05-01'),
(14, 12, 'Test 8', 0.25, '2024-04-09');


--
-- Table `Eleves`
--

CREATE TABLE `Eleves` (
  `idEleve` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `sexe` varchar(1) NOT NULL,
  `dateNaissance` date NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `idClasse` int(11) NOT NULL,
  `respLegal1Id` int(11) NOT NULL,
  `respLegal2Id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Eleves` (`idEleve`, `idUtilisateur`, `nom`, `prenom`, `sexe`, `dateNaissance`, `adresse`, `telephone`, `email`, `idClasse`, `respLegal1Id`, `respLegal2Id`) VALUES
(1, 1, 'DELHAYE', 'Tony', 'G', '2009-01-21', '2 avenue Philippine Diaz, Grenier 19414, France', '02 66 07 95 23', NULL, 1, 1, NULL),
(2, 2, 'DESCAMPS', 'Manon', 'F', '2009-04-01', '42 place Nathalie Gauthier, Dos Santos 48994, France', NULL, NULL, 3, 2, 3),
(3, 3, 'GRONDIN', 'Aurélien', 'G', '2009-11-03', '80 impasse Thibault, Lebon 41380, France', NULL, NULL, 2, 4, 5),
(4, 4, 'PAUL', 'Alison', 'F', '2009-08-04', '77 avenue Virginie Julien, Levyboeuf 08182, France', NULL, NULL, 1, 6, 7),
(5, 5, 'BLOT', 'Laurie', 'F', '2010-06-21', '11 boulevard Gallet, Payet 62546, France', '01 27 76 84 71', NULL, 2, 8, 9),
(6, 6, 'FUCHS', 'Agathe', 'F', '2010-12-02', '98 avenue François Clement, Thierry 86131, France', '01 34 24 14 12', NULL, 4, 10, 11),
(7, 7, 'GRANGER', 'Augustin', 'G', '2010-09-26', '7 rue de Duhamel, Mallet 75153, France', NULL, NULL, 4, 12, 13),
(8, 8, 'WANG', 'Julien', 'G', '2010-07-12', '61 rue Alphonse Marin, Roy-sur-Fouquet 25727, France', NULL, NULL, 3, 14, 15);


--
-- Table `Enseignement`
--

CREATE TABLE `Enseignement` (
  `idEnseignement` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idProf` int(11) NOT NULL,
  `idClasse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Enseignement` (`idEnseignement`, `idProf`, `idClasse`) VALUES
(1, 1, 1),
(2, 1, 4),
(3, 2, 3),
(4, 2, 2),
(5, 3, 4),
(6, 3, 2),
(7, 4, 3),
(8, 4, 1),
(9, 5, 3),
(10, 5, 4),
(11, 6, 2),
(12, 6, 1),
(13, 7, 3),
(14, 7, 1),
(15, 8, 4),
(16, 8, 2),
(17, 9, 1),
(18, 9, 2),
(19, 10, 3),
(20, 10, 4),
(21, 11, 4),
(22, 11, 3),
(23, 12, 2),
(24, 12, 1),
(25, 13, 4),
(26, 13, 3),
(27, 14, 1),
(28, 14, 2),
(29, 15, 2),
(30, 15, 3),
(31, 16, 1),
(32, 16, 4);


--
-- Table `Matiere`
--

CREATE TABLE `Matiere` (
  `idMatiere` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Matiere` (`idMatiere`, `nom`, `description`) VALUES
(1, 'Maths', 'Mathématiques'),
(2, 'Fran', 'Français'),
(3, 'Ang', 'Anglais'),
(4, 'All', 'Allemand'),
(5, 'PC', 'Physique Chimie'),
(6, 'SVT', 'Sciences de la vie et de la Terre'),
(7, 'EPS', 'Éducation physique et sportive'),
(8, 'HG', 'Histoire Géographie');


--
-- Table `Notes`
--

CREATE TABLE `Notes` (
  `idNote` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idEleve` int(11) NOT NULL,
  `idControle` int(11) NOT NULL,
  `note` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Notes` (`idNote`, `idEleve`, `idControle`, `note`) VALUES
(1, 1, 1, '13'),
(2, 1, 2, '17'),
(3, 1, 3, '7'),
(4, 2, 4, '18'),
(5, 3, 5, '4'),
(6, 3, 6, '12'),
(7, 4, 1, '20'),
(8, 4, 2, '5'),
(9, 4, 3, '10'),
(10, 5, 5, '16'),
(11, 5, 6, '9'),
(12, 8, 4, '2'),
(13, 1, 7, '16'),
(14, 4, 7, 'Abs'),
(15, 6, 8, '12'),
(16, 7, 8, '7'),
(17, 1, 9, '17.5'),
(18, 4, 9, '6'),
(19, 1, 10, '2'),
(20, 4, 10, 'N.Not'),
(21, 1, 11, '18'),
(22, 4, 11, '20'),
(23, 1, 12, '0'),
(24, 4, 12, '11'),
(25, 1, 13, 'Disp'),
(26, 4, 13, '9'),
(27, 1, 14, '20'),
(28, 4, 14, NULL);


--
-- Table `Parents`
--

CREATE TABLE `Parents` (
  `idParent` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `sexe` varchar(1) NOT NULL,
  `dateNaissance` date NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Parents` (`idParent`, `idUtilisateur`, `nom`, `prenom`, `sexe`, `dateNaissance`, `adresse`, `telephone`, `email`) VALUES
(1, 9, 'LECOMTE', 'Isabelle', 'F', '1974-01-05', '2 avenue Philippine Diaz, Grenier 19414, France', NULL, NULL),
(2, 10, 'DESCAMPS', 'Éric', 'M', '1973-02-12', '42 place Nathalie Gauthier, Dos Santos 48994, France', NULL, NULL),
(3, 11, 'DESCAMPS', 'Martine', 'F', '1968-07-28', '42 place Nathalie Gauthier, Dos Santos 48994, France', '02 66 98 63 05', NULL),
(4, 12, 'GRONDIN', 'Jean', 'M', '1969-03-12', '80 impasse Thibault, Lebon 41380, France', NULL, NULL),
(5, 13, 'GRONDIN', 'Nathalie', 'F', '1976-06-12', '80 impasse Thibault, Lebon 41380, France', '03 27 68 67 15', NULL),
(6, 14, 'PAUL', 'Franck', 'M', '1970-03-16', '77 avenue Virginie Julien, Levyboeuf 08182, France', NULL, NULL),
(7, 15, 'PAUL', 'Camille', 'F', '1985-01-26', '77 avenue Virginie Julien, Levyboeuf 08182, France', '06 80 77 56 53', NULL),
(8, 16, 'BLOT', 'Jean-Pierre', 'M', '1987-07-29', '11 boulevard Gallet, Payet 62546, France', NULL, NULL),
(9, 17, 'BLOT', 'Sylviane', 'F', '1983-12-23', '11 boulevard Gallet, Payet 62546, France', NULL, NULL),
(10, 18, 'FUCHS', 'Thierry', 'M', '1985-06-01', '98 avenue François Clement, Thierry 86131, France', '07 35 22 81 26', NULL),
(11, 19, 'FUCHS', 'Sandrine', 'F', '1985-05-25', '98 avenue François Clement, Thierry 86131, France', NULL, NULL),
(12, 20, 'GRANGER', 'Stanislas', 'M', '1968-03-17', '7 rue de Duhamel, Mallet 75153, France', NULL, NULL),
(13, 21, 'GRANGER', 'Marie-Ange', 'F', '1968-04-20', '7 rue de Duhamel, Mallet 75153, France', NULL, NULL),
(14, 22, 'WANG', 'Daniel', 'M', '1977-10-22', '61 rue Alphonse Marin, Roy-sur-Fouquet 25727, France', '07 68 98 55 35', NULL),
(15, 23, 'WANG', 'Christine', 'F', '1979-10-03', '61 rue Alphonse Marin, Roy-sur-Fouquet 25727, France', NULL, NULL);


--
-- Table `Profs`
--

CREATE TABLE `Profs` (
  `idProf` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `idMatiere` int(11) NOT NULL,
  `sexe` varchar(1) NOT NULL,
  `dateNaissance` date NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `Profs` (`idProf`, `idUtilisateur`, `nom`, `prenom`, `idMatiere`, `sexe`, `dateNaissance`, `adresse`, `telephone`, `email`) VALUES
(1, 24, 'AZZURRO', 'Giovanni', 1, 'M', '1993-07-16', '75 avenue Tanguy, Leclercdan 90660, France', NULL, NULL),
(2, 25, 'WEISS', 'Jéméry', 1, 'M', '1975-05-05', '8 chemin de Roussel, Laurent 07268, France', NULL, NULL),
(3, 26, 'RAMON', 'Stéphane', 2, 'M', '1995-01-07', '83 rue Jeanne Laroche, Guibert 89991, France', NULL, NULL),
(4, 27, 'DOUCET', 'Laure', 2, 'F', '1971-05-25', '46 boulevard David, Guillaume-sur-Ribeiro 53881, France', '03 50 66 18 21', NULL),
(5, 28, 'GAUDIN', 'Florence', 3, 'F', '1988-02-15', '57 rue de Besnard, Joubert 78362, France', NULL, NULL),
(6, 29, 'GALLET', 'Benjamin', 3, 'M', '1962-04-02', '83 rue Robert Durand, Michaud-la-Forêt 14769, France', NULL, NULL),
(7, 30, 'MARTINEZ', 'Laura', 4, 'F', '1978-12-21', '27 impasse de Perez, Lamy 54160, France', NULL, NULL),
(8, 31, 'ALVAREZ', 'Sabrina', 4, 'F', '1989-06-25', '7 place Arnaud, Legrosnec 30563, France', '01 33 41 62 77', NULL),
(9, 32, 'PUJOL', 'Philippe', 5, 'M', '1977-08-18', '5 rue de Mahe, Marin-sur-Mer 75652, France', NULL, NULL),
(10, 33, 'ROUX', 'Nawal', 5, 'F', '1973-03-05', '4 boulevard Lucas Thomas, Gimenez 49249, France', '08 99 17 88 29', NULL),
(11, 34, 'MILLOT', 'Pauline', 6, 'F', '1982-10-06', '94 rue de Colin, Guilletnec 72343, France', '01 15 61 07 32', NULL),
(12, 35, 'JOLY', 'Andrea', 6, 'F', '1961-10-12', '369 boulevard de Renault, Voisin 32945, France', '04 93 13 58 64', NULL),
(13, 36, 'CECCACI', 'Thomas', 7, 'M', '1970-04-17', '9 chemin Bertrand, Chauvin 70164, France', '01 01 70 00 88', NULL),
(14, 37, 'BROWN', 'Julie', 7, 'F', '1983-04-17', '25 rue Patricia Clement, Le Gall 82519, France', '03 03 22 86 91', NULL),
(15, 38, 'SIMON', 'Amandine', 8, 'F', '1973-06-01', '68 rue Philippine Pottier, Renaud 85637, France', '09 83 27 06 62', NULL),
(16, 39, 'LACAZE', 'Hugo', 8, 'M', '1981-12-01', '80 rue Garnier, Gregoire 10825, France', '04 99 43 46 19', NULL);


--
-- Table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `idUtilisateur` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('Eleve','Parent','Professeur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `Utilisateur` (`idUtilisateur`, `login`, `mdp`, `role`) VALUES
(1, 'eleve_demo', '$2y$10$Fu3djB.tdWL/XRXNXWRN0.OEErFKdASIl8Cb2l.CdTYcl7zxmKSNq', 'Eleve'),
(2, 'mdescamps_eleve', '$2y$10$9I3kyM/upjMrv6IIvw4RR.jDZr1BQUF5WItNUkiV/1YmaPquFXgLS', 'Eleve'),
(3, 'agrondin', '$2y$10$nsWsSKHl28UDJYZq13m5WOozohmPMTVPZlDe7XwMIUw5eM85RzUq.', 'Eleve'),
(4, 'apaul', '$2y$10$nMSaY.qgV62a8tXMUl4ECu2UHZrXcaBiXadCTbUO232ImJFWmCqEa', 'Eleve'),
(5, 'lblot', '$2y$10$ESMWkyNe1RaZJMBRS9hbPu8N/UhXBTPRl7XRbXOfvYcoCiX5FYXnC', 'Eleve'),
(6, 'afuchs', '$2y$10$Vb5xmYjj.51cXpcMwzUfNeJKs1INmlBpEK709c2RuGAy9cUSOGNK2', 'Eleve'),
(7, 'agranger', '$2y$10$u.CxqxSNvP5bMLMVMIOFsOTVxcHnvzRPfLL8ww2H2jySbGkMYIMZe', 'Eleve'),
(8, 'jwang', '$2y$10$LGGqf5zliv.xLLc1RLu6yOi7hW9Y1DD6fMnjHL0VwFjdMqHv5005O', 'Eleve'),
(9, 'ilecomte', '$2y$10$Imxv8A2siYigltSCoBKo7O0YAcIvt1mbdbPuRUihanVxHrcp5JcPm', 'Parent'),
(10, 'edescamps', '$2y$10$d1FwLatBWkh0fsrgqZZZ7efe4JZcm0ceErotOqvr3vcLL8yix0w2W', 'Parent'),
(11, 'mdescamps_parent', '$2y$10$IukXOM9LlRRlWS0OfJBL/.nz140va.vXRoLDqVtisL7TllJz2wO..', 'Parent'),
(12, 'jgrondin', '$2y$10$iod65tBjUem8um4o6AzLMe832zA7ofRnyEmBG9cI7ppsYckI9xkNK', 'Parent'),
(13, 'ngrondin', '$2y$10$JIqwCYgT7mlw4lFAMeL0JedF5YoMgvscliFAHLC.k/9TvrGQLwpGa', 'Parent'),
(14, 'fpaul', '$2y$10$hl7lzB87za2oJugjGxFKfuKjuLPhpiz4Dq2zeDAd.lz0n4hE3NONW', 'Parent'),
(15, 'parent_demo', '$2y$10$2Wu9Q6o1U.Wbe/6HYkDZeuSObNfT9zj4zIKni7IWbcy4hSvQBlbd2', 'Parent'),
(16, 'jblot', '$2y$10$OK9iLLk41dhlKGKif2zjsuTxolnMcT/qdTs9VQ9yG.PKJA9aJ99i2', 'Parent'),
(17, 'sblot', '$2y$10$kI2gbpgCwlVvYIt5jXiGD.nKzyfFMDpH7oGknYmcwZ/vncqosIUwu', 'Parent'),
(18, 'tfuchs', '$2y$10$0/buu.ShjngNyoo2cYU7PO0YAJ87im1hjS1tQAIv2J60dZgrTIVce', 'Parent'),
(19, 'sfuchs', '$2y$10$fbBB4Gp4RWGU0RL1MVgfL.aQZJ2rf2BUZQAOKW88YGOrcRrknBgTK', 'Parent'),
(20, 'sgranger', '$2y$10$tUbfA5LQfEii8jEdLX7VS.npYDZ6ixFXWkqlraQR9nTU.GLIxIc2y', 'Parent'),
(21, 'mgranger', '$2y$10$S3LT8wvvW3b/zFsJG4jlBOXBFtPAOyrqHd1PAhLn8S.99JEvbrK5i', 'Parent'),
(22, 'dwang', '$2y$10$PQArczla8dVlBHKMBFACzOtxZ2RNZRyVdf4gudxLLjiHUki213dJ2', 'Parent'),
(23, 'cwang', '$2y$10$33/CJQCmgIW16cm4BzcDY./aizHtrNpzaYM3nakiwO2ARiubfKmkW', 'Parent'),
(24, 'gazzuro', '$2y$10$ZGQuxqMMetc4ZpJwwWokh.iTfyZCjqLQzvhNHYIlx4nDRbTzf9VG6', 'Professeur'),
(25, 'jweiss', '$2y$10$wlxBx8uXx3gdr73XxblSCuhmI9GwZrIJSZ4wtR0ksX4w5yWZHZfO2', 'Professeur'),
(26, 'sramon', '$2y$10$0zcp2J8RJj3Orgv0OczXUO91jodZANlx689WzWKdZOcKMahQ6mr7G', 'Professeur'),
(27, 'ldoucet', '$2y$10$BreeNAU.s4zqGkZgxqv1Eeg./1LjV7MGYjhS4IiDtYE9hzc1lJ5M.', 'Professeur'),
(28, 'fgaudin', '$2y$10$XyF.uhaUmg0koD8/fptbs.c8ZciXg5wSYUsX70hmnn1KCNn.aZ9qa', 'Professeur'),
(29, 'bgallet', '$2y$10$tmkTnkxBH1Utrb9s91/a7OvQ2ZvYec.U2bVK/8M3ybRCjtNyY02de', 'Professeur'),
(30, 'lmartinez', '$2y$10$V09yYK.DG.QxYLfFRMsAyeZ64tpsCTlNs.XOTSjRFu5aIK4dwjwae', 'Professeur'),
(31, 'salvarez', '$2y$10$e5UcEotbc0HFBT7CmbE3XO.VYst/frc9mJI7B2UtwfEwZZPIIHN42', 'Professeur'),
(32, 'ppujol', '$2y$10$ny2M0kkEc6vnIAsYoj4Of.qec/A/.XsFNWXt/IEJhe5XjE/Umx51y', 'Professeur'),
(33, 'nroux', '$2y$10$oALb.IcgnPK4qtllNMmXt.MQs3AvzUE0.q4mucqfnrs/N5VQKh9ci', 'Professeur'),
(34, 'pmillot', '$2y$10$HdX/gPEwqNOYldBIdpsteOORTQ373q/t.zASvo/pFRQGj9Jc5hi/i', 'Professeur'),
(35, 'ajoly', '$2y$10$z7GmTzjO2I3NDRsbTP5R0O4/Jao.UjZNohKCBYgODB.LUOaPIzHcS', 'Professeur'),
(36, 'tceccaci', '$2y$10$u/HAfA9yZQgfsIGlGysej.x9IR2/QZVzcppPfIiRFcX6ilII6r8hG', 'Professeur'),
(37, 'jbrown', '$2y$10$3I3Mv1Jrswp6tMh/S8igq.wMYy8ODkkS14UaLnPbolugrwxcYy88K', 'Professeur'),
(38, 'asimon', '$2y$10$OLVnEB873ZFh71q3AoCaU.g0ea2vG/si1TtIv9TW7Rqhc.2R0yHqK', 'Professeur'),
(39, 'prof_demo', '$2y$10$CL/Q0bh6tDoXHzRWDUP1Ueyh64YVHkE1k2VC5bFCYi3A.x5OpW/H.', 'Professeur');

ALTER TABLE `Utilisateur`
  ADD UNIQUE KEY `uniq_login` (`login`);