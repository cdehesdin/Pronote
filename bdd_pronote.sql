-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 19 jan. 2025 à 14:23
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pronote`
--

-- --------------------------------------------------------

--
-- Structure de la table `AppClasse`
--

CREATE TABLE `AppClasse` (
  `idAppClasse` int(11) NOT NULL,
  `idEnseignement` int(11) NOT NULL,
  `app` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `AppClasse`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `AppEleve`
--

CREATE TABLE `AppEleve` (
  `idAppEleve` int(11) NOT NULL,
  `idEnseignement` int(11) NOT NULL,
  `idEleve` int(11) NOT NULL,
  `app` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `AppEleve`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `Classe`
--

CREATE TABLE `Classe` (
  `idClasse` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `PPID` int(11) NOT NULL,
  `appreciationPP` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Classe`
--

INSERT INTO `Classe` (`idClasse`, `nom`, `PPID`, `appreciationPP`) VALUES
(1, '3A', 4, NULL),
(2, '3B', 8, NULL),
(3, '4A', 12, NULL),
(4, '4B', 10, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `Controles`
--

CREATE TABLE `Controles` (
  `idControle` int(11) NOT NULL,
  `idEnseignement` int(11) NOT NULL,
  `nomControle` varchar(255) NOT NULL,
  `coefficient` float NOT NULL,
  `dates` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Controles`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `Eleves`
--

CREATE TABLE `Eleves` (
  `idEleve` int(11) NOT NULL,
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

--
-- Déchargement des données de la table `Eleves`
--

INSERT INTO `Eleves` (`idEleve`, `idUtilisateur`, `nom`, `prenom`, `sexe`, `dateNaissance`, `adresse`, `telephone`, `email`, `idClasse`, `respLegal1Id`, `respLegal2Id`) VALUES
(1, 1, 'DELHAYE', 'Tony', 'G', '2009-01-21', '2 avenue Philippine Diaz, Grenier 19414, France', '02 66 07 95 23', NULL, 1, 1, NULL),
(2, 2, 'DESCAMPS', 'Manon', 'F', '2009-04-01', '42 place Nathalie Gauthier, Dos Santos 48994, France', NULL, NULL, 3, 2, 3),
(3, 3, 'GRONDIN', 'Aurélien', 'G', '2009-11-03', '80 impasse Thibault, Lebon 41380, France', NULL, NULL, 2, 4, 5),
(4, 4, 'PAUL', 'Alison', 'F', '2009-08-04', '77 avenue Virginie Julien, Levyboeuf 08182, France', NULL, NULL, 1, 6, 7),
(5, 5, 'BLOT', 'Laurie', 'F', '2010-06-21', '11 boulevard Gallet, Payet 62546, France', '01 27 76 84 71', NULL, 2, 8, 9),
(6, 6, 'FUCHS', 'Agathe', 'F', '2010-12-02', '98 avenue François Clement, Thierry 86131, France', '01 34 24 14 12', NULL, 4, 10, 11),
(7, 7, 'GRANGER', 'Augustin', 'G', '2010-09-26', '7 rue de Duhamel, Mallet 75153, France', NULL, NULL, 4, 12, 13),
(8, 8, 'WANG', 'Julien', 'G', '2010-07-12', '61 rue Alphonse Marin, Roy-sur-Fouquet 25727, France', NULL, NULL, 3, 14, 15);

-- --------------------------------------------------------

--
-- Structure de la table `Enseignement`
--

CREATE TABLE `Enseignement` (
  `idEnseignement` int(11) NOT NULL,
  `idProf` int(11) NOT NULL,
  `idClasse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Enseignement`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `Matiere`
--

CREATE TABLE `Matiere` (
  `idMatiere` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Matiere`
--

INSERT INTO `Matiere` (`idMatiere`, `nom`, `description`) VALUES
(1, 'Maths', 'Mathématiques'),
(2, 'Fran', 'Français'),
(3, 'Ang', 'Anglais'),
(4, 'All', 'Allemand'),
(5, 'PC', 'Physique Chimie'),
(6, 'SVT', 'Sciences de la vie et de la Terre'),
(7, 'EPS', 'Éducation physique et sportive'),
(8, 'HG', 'Histoire Géographie');

-- --------------------------------------------------------

--
-- Structure de la table `Notes`
--

CREATE TABLE `Notes` (
  `idNote` int(11) NOT NULL,
  `idEleve` int(11) NOT NULL,
  `idControle` int(11) NOT NULL,
  `note` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Notes`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `Parents`
--

CREATE TABLE `Parents` (
  `idParent` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `sexe` varchar(1) NOT NULL,
  `dateNaissance` date NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Parents`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `Profs`
--

CREATE TABLE `Profs` (
  `idProf` int(11) NOT NULL,
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

--
-- Déchargement des données de la table `Profs`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `idUtilisateur` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `Utilisateur`
--

INSERT INTO `Utilisateur` (`idUtilisateur`, `login`, `mdp`, `role`) VALUES
(1, 'eleve_demo', 'demoEleve', 'Eleve'),
(2, 'mdescamps', 'mdescamps', 'Eleve'),
(3, 'agrondin', 'agrondin', 'Eleve'),
(4, 'apaul', 'apaul', 'Eleve'),
(5, 'lblot', 'lblot', 'Eleve'),
(6, 'afuchs', 'afuchs', 'Eleve'),
(7, 'agranger', 'agranger', 'Eleve'),
(8, 'jwang', 'jwang', 'Eleve'),
(9, 'ilecomte', 'ilecomte', 'Parent'),
(10, 'edescamps', 'edescamps', 'Parent'),
(11, 'mdescamps', 'mdescamps', 'Parent'),
(12, 'jgrondin', 'jgrondin', 'Parent'),
(13, 'ngrondin', 'ngrondin', 'Parent'),
(14, 'fpaul', 'fpaul', 'Parent'),
(15, 'parent_demo', 'demoParent', 'Parent'),
(16, 'jblot', 'jblot', 'Parent'),
(17, 'sblot', 'sblot', 'Parent'),
(18, 'tfuchs', 'tfuchs', 'Parent'),
(19, 'sfuchs', 'sfuchs', 'Parent'),
(20, 'sgranger', 'sgranger', 'Parent'),
(21, 'mgranger', 'mgranger', 'Parent'),
(22, 'dwang', 'dwang', 'Parent'),
(23, 'cwang', 'cwang', 'Parent'),
(24, 'gazzuro', 'gazzuro', 'Professeur'),
(25, 'jweiss', 'jweiss', 'Professeur'),
(26, 'sramon', 'sramon', 'Professeur'),
(27, 'ldoucet', 'ldoucet', 'Professeur'),
(28, 'fgaudin', 'fgaudin', 'Professeur'),
(29, 'bgallet', 'bgallet', 'Professeur'),
(30, 'lmartinez', 'lmartinez', 'Professeur'),
(31, 'salvarez', 'salvarez', 'Professeur'),
(32, 'ppujol', 'ppujol', 'Professeur'),
(33, 'nroux', 'nroux', 'Professeur'),
(34, 'pmillot', 'pmillot', 'Professeur'),
(35, 'ajoly', 'ajoly', 'Professeur'),
(36, 'tceccaci', 'tceccaci', 'Professeur'),
(37, 'jbrown', 'jbrown', 'Professeur'),
(38, 'asimon', 'asimon', 'Professeur'),
(39, 'prof_demo', 'demoProf', 'Professeur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `AppClasse`
--
ALTER TABLE `AppClasse`
  ADD PRIMARY KEY (`idAppClasse`);

--
-- Index pour la table `AppEleve`
--
ALTER TABLE `AppEleve`
  ADD PRIMARY KEY (`idAppEleve`);

--
-- Index pour la table `Classe`
--
ALTER TABLE `Classe`
  ADD PRIMARY KEY (`idClasse`);

--
-- Index pour la table `Controles`
--
ALTER TABLE `Controles`
  ADD PRIMARY KEY (`idControle`);

--
-- Index pour la table `Eleves`
--
ALTER TABLE `Eleves`
  ADD PRIMARY KEY (`idEleve`);

--
-- Index pour la table `Enseignement`
--
ALTER TABLE `Enseignement`
  ADD PRIMARY KEY (`idEnseignement`);

--
-- Index pour la table `Matiere`
--
ALTER TABLE `Matiere`
  ADD PRIMARY KEY (`idMatiere`);

--
-- Index pour la table `Notes`
--
ALTER TABLE `Notes`
  ADD PRIMARY KEY (`idNote`);

--
-- Index pour la table `Parents`
--
ALTER TABLE `Parents`
  ADD PRIMARY KEY (`idParent`);

--
-- Index pour la table `Profs`
--
ALTER TABLE `Profs`
  ADD PRIMARY KEY (`idProf`);

--
-- Index pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`idUtilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `AppClasse`
--
ALTER TABLE `AppClasse`
  MODIFY `idAppClasse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `AppEleve`
--
ALTER TABLE `AppEleve`
  MODIFY `idAppEleve` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `Classe`
--
ALTER TABLE `Classe`
  MODIFY `idClasse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `Controles`
--
ALTER TABLE `Controles`
  MODIFY `idControle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `Eleves`
--
ALTER TABLE `Eleves`
  MODIFY `idEleve` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `Enseignement`
--
ALTER TABLE `Enseignement`
  MODIFY `idEnseignement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `Matiere`
--
ALTER TABLE `Matiere`
  MODIFY `idMatiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `Notes`
--
ALTER TABLE `Notes`
  MODIFY `idNote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `Parents`
--
ALTER TABLE `Parents`
  MODIFY `idParent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `Profs`
--
ALTER TABLE `Profs`
  MODIFY `idProf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
