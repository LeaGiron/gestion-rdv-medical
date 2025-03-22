-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 22 mars 2025 à 15:50
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_de_rdv_medicaux`
--

-- --------------------------------------------------------

--
-- Structure de la table `horaires_medecins`
--

CREATE TABLE `horaires_medecins` (
  `hor_id_horaire` int(11) NOT NULL,
  `hor_id_medecin` int(11) NOT NULL,
  `hor_heure` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `horaires_medecins`
--

INSERT INTO `horaires_medecins` (`hor_id_horaire`, `hor_id_medecin`, `hor_heure`) VALUES
(1, 1, '08:00:00'),
(2, 1, '08:30:00'),
(3, 1, '09:00:00'),
(4, 1, '09:30:00'),
(5, 1, '10:00:00'),
(6, 1, '10:30:00'),
(7, 1, '11:00:00'),
(8, 1, '11:30:00'),
(9, 1, '12:00:00'),
(10, 1, '13:30:00'),
(11, 1, '14:00:00'),
(12, 1, '14:30:00'),
(13, 1, '15:00:00'),
(14, 1, '15:30:00'),
(15, 1, '16:00:00'),
(16, 1, '16:30:00'),
(17, 1, '17:00:00'),
(18, 1, '17:30:00'),
(19, 1, '18:00:00'),
(20, 2, '08:00:00'),
(21, 2, '08:30:00'),
(22, 2, '09:00:00'),
(23, 2, '09:30:00'),
(24, 2, '10:00:00'),
(25, 2, '10:30:00'),
(26, 2, '11:00:00'),
(27, 2, '11:30:00'),
(28, 2, '12:00:00'),
(29, 2, '13:30:00'),
(30, 2, '14:00:00'),
(31, 2, '14:30:00'),
(32, 2, '15:00:00'),
(33, 2, '15:30:00'),
(34, 2, '16:00:00'),
(35, 2, '16:30:00'),
(36, 2, '17:00:00'),
(37, 2, '17:30:00'),
(38, 2, '18:00:00'),
(39, 3, '08:00:00'),
(40, 3, '08:30:00'),
(41, 3, '09:00:00'),
(42, 3, '09:30:00'),
(43, 3, '10:00:00'),
(44, 3, '10:30:00'),
(45, 3, '11:00:00'),
(46, 3, '11:30:00'),
(47, 3, '12:00:00'),
(48, 3, '13:30:00'),
(49, 3, '14:00:00'),
(50, 3, '14:30:00'),
(51, 3, '15:00:00'),
(52, 3, '15:30:00'),
(53, 3, '16:00:00'),
(54, 3, '16:30:00'),
(55, 3, '17:00:00'),
(56, 3, '17:30:00'),
(57, 3, '18:00:00'),
(58, 4, '08:00:00'),
(59, 4, '08:30:00'),
(60, 4, '09:00:00'),
(61, 4, '09:30:00'),
(62, 4, '10:00:00'),
(63, 4, '10:30:00'),
(64, 4, '11:00:00'),
(65, 4, '11:30:00'),
(66, 4, '12:00:00'),
(67, 4, '13:30:00'),
(68, 4, '14:00:00'),
(69, 4, '14:30:00'),
(70, 4, '15:00:00'),
(71, 4, '15:30:00'),
(72, 4, '16:00:00'),
(73, 4, '16:30:00'),
(74, 4, '17:00:00'),
(75, 4, '17:30:00'),
(76, 4, '18:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `medecins`
--

CREATE TABLE `medecins` (
  `med_id_medecin` int(11) NOT NULL,
  `util_id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `medecins`
--

INSERT INTO `medecins` (`med_id_medecin`, `util_id_utilisateur`) VALUES
(1, 2),
(2, 4),
(3, 6),
(4, 8);

-- --------------------------------------------------------

--
-- Structure de la table `patients`
--

CREATE TABLE `patients` (
  `pat_id_patient` int(11) NOT NULL,
  `util_id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `patients`
--

INSERT INTO `patients` (`pat_id_patient`, `util_id_utilisateur`) VALUES
(1, 1),
(2, 3),
(3, 5),
(4, 7);

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `rdv_id_rendez_vous` int(11) NOT NULL,
  `rdv_id_patient` int(11) DEFAULT NULL,
  `rdv_id_medecin` int(11) DEFAULT NULL,
  `rdv_date_rendez_vous` date NOT NULL,
  `rdv_heure_rendez_vous` time NOT NULL,
  `rdv_statut_rendez_vous` enum('Confirmé','Annulé','En attente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`rdv_id_rendez_vous`, `rdv_id_patient`, `rdv_id_medecin`, `rdv_date_rendez_vous`, `rdv_heure_rendez_vous`, `rdv_statut_rendez_vous`) VALUES
(1, 1, 2, '2025-02-20', '08:00:00', 'Annulé'),
(3, 3, 4, '2025-02-22', '14:00:00', 'Confirmé'),
(4, 4, 2, '2025-02-23', '16:00:00', 'Annulé'),
(10, 3, 1, '2025-03-16', '14:00:00', 'En attente'),
(11, 4, 2, '2025-03-17', '09:00:00', 'En attente'),
(32, 1, 1, '2025-03-19', '10:00:00', 'Confirmé'),
(66, 2, 4, '2025-03-27', '15:00:00', 'En attente'),
(67, 2, 3, '2025-03-25', '08:00:00', 'En attente'),
(68, 4, 3, '2025-03-28', '14:00:00', 'Confirmé');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `util_id_utilisateur` int(11) NOT NULL,
  `util_nom` varchar(50) NOT NULL,
  `util_prenom` varchar(50) NOT NULL,
  `util_email` varchar(100) NOT NULL,
  `util_mot_de_passe` varchar(255) NOT NULL,
  `util_telephone` varchar(10) DEFAULT NULL,
  `util_role` enum('patient','medecin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`util_id_utilisateur`, `util_nom`, `util_prenom`, `util_email`, `util_mot_de_passe`, `util_telephone`, `util_role`) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@email.com', '$2y$10$o9ZY/V2TDgmFZeN7e9O/ju2tzfIyi9zVtd.DmqKrDWjuUdY1jOgcm', '0601020304', 'patient'),
(2, 'Martin', 'Alice', 'alice.martin@email.com', '$2y$10$sCuIRv4Ky9J26.28U02jj.8eWeRx1RNVpDSEF1S8fmqR2PEzXXhrC', '0611223344', 'medecin'),
(3, 'Lemoine', 'Sophie', 'sophie.lemoine@email.com', '$2y$10$AyMpPEf4ZFEDJ6/qBj86o.Jiy3quOsxGRrdHolBvpYeqfOd1W2T2e', '0622334455', 'patient'),
(4, 'Bertrand', 'Thomas', 'thomas.bertrand@email.com', '$2y$10$3yRuso7MwZKyCQV5n/VBfuHRg0J7s5HplZFsnd7/r7NwkGc2LGsyS', '0633445566', 'medecin'),
(5, 'Garnier', 'Camille', 'camille.garnier@email.com', '$2y$10$uTpFEV.8VI2yLHoZax7vseG/sA92SnJ5vV/iPNkgRQ0M2.mfAePcy', '0644556677', 'patient'),
(6, 'Morel', 'Nicolas', 'nicolas.morel@email.com', '$2y$10$eF5cRJvf.4SYB9t6iMO.8ezw/0EP2lnhAQwKflieq06/xJ2Ulqqpu', '0655667788', 'medecin'),
(7, 'Lefevre', 'Chloé', 'chloe.lefevre@email.com', '$2y$10$EbnNmEfrCheXuLkMHKLgburyBnSVbVElVf6YjXGwMpyzQQRgcljgy', '0666778899', 'patient'),
(8, 'Durand', 'Pierre', 'pierre.durand@email.com', '$2y$10$s.Kd8aFZ3Z/YecoKDRQIV.cC50NEEDsVbqQIVraJEwSyD.XZUfZTq', '0677889900', 'medecin');

--
-- Déclencheurs `utilisateurs`
--
DELIMITER $$
CREATE TRIGGER `inserer_medecin_apres_utilisateur` AFTER INSERT ON `utilisateurs` FOR EACH ROW BEGIN
	IF NEW.util_role = 'medecin' THEN
    	INSERT INTO Medecins (util_id_utilisateur)
        VALUES (NEW.util_id_utilisateur);
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `inserer_patient_apres_utilisateur` AFTER INSERT ON `utilisateurs` FOR EACH ROW BEGIN
	IF NEW.util_role = 'patient' THEN
    	INSERT INTO Patients (util_id_utilisateur)
        VALUES (NEW.util_id_utilisateur);
	END IF;
END
$$
DELIMITER ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `horaires_medecins`
--
ALTER TABLE `horaires_medecins`
  ADD PRIMARY KEY (`hor_id_horaire`),
  ADD KEY `fk_hor_medecin` (`hor_id_medecin`);

--
-- Index pour la table `medecins`
--
ALTER TABLE `medecins`
  ADD PRIMARY KEY (`med_id_medecin`),
  ADD KEY `fk_utilisateur` (`util_id_utilisateur`);

--
-- Index pour la table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`pat_id_patient`),
  ADD KEY `util_id_utilisateur` (`util_id_utilisateur`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`rdv_id_rendez_vous`),
  ADD KEY `rendez_vous_ibfk_1` (`rdv_id_patient`),
  ADD KEY `rendez_vous_ibfk_2` (`rdv_id_medecin`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`util_id_utilisateur`),
  ADD UNIQUE KEY `util_email` (`util_email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `horaires_medecins`
--
ALTER TABLE `horaires_medecins`
  MODIFY `hor_id_horaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT pour la table `medecins`
--
ALTER TABLE `medecins`
  MODIFY `med_id_medecin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `patients`
--
ALTER TABLE `patients`
  MODIFY `pat_id_patient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `rdv_id_rendez_vous` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `util_id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `horaires_medecins`
--
ALTER TABLE `horaires_medecins`
  ADD CONSTRAINT `fk_hor_medecin` FOREIGN KEY (`hor_id_medecin`) REFERENCES `medecins` (`med_id_medecin`) ON DELETE CASCADE;

--
-- Contraintes pour la table `medecins`
--
ALTER TABLE `medecins`
  ADD CONSTRAINT `fk_utilisateur` FOREIGN KEY (`util_id_utilisateur`) REFERENCES `utilisateurs` (`util_id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`util_id_utilisateur`) REFERENCES `utilisateurs` (`util_id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`rdv_id_patient`) REFERENCES `patients` (`pat_id_patient`) ON DELETE CASCADE,
  ADD CONSTRAINT `rendez_vous_ibfk_2` FOREIGN KEY (`rdv_id_medecin`) REFERENCES `medecins` (`med_id_medecin`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
