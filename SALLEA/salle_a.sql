-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  sam. 14 avr. 2018 à 07:39
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `salle_a`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id_avis` int(3) NOT NULL AUTO_INCREMENT,
  `id_membre` int(3) NOT NULL,
  `id_salle` int(3) NOT NULL,
  `commentaire` text NOT NULL,
  `note` int(2) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  PRIMARY KEY (`id_avis`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id_avis`, `id_membre`, `id_salle`, `commentaire`, `note`, `date_enregistrement`) VALUES
(1, 7, 5, 'belle salle', 4, '2018-04-11 14:16:03'),
(2, 9, 10, 'grande salle très moderne et confortable', 5, '2018-03-31 16:36:28'),
(3, 8, 8, 'salle très lumineuse et calme', 3, '2018-04-08 16:37:51');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` int(3) NOT NULL AUTO_INCREMENT,
  `id_membre` int(3) NOT NULL,
  `id_produit` int(3) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  PRIMARY KEY (`id_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

DROP TABLE IF EXISTS `membre`;
CREATE TABLE IF NOT EXISTS `membre` (
  `id_membre` int(3) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(20) NOT NULL,
  `mdp` varchar(60) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `civilite` enum('m','f') NOT NULL,
  `statut` int(1) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  PRIMARY KEY (`id_membre`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` int(3) NOT NULL AUTO_INCREMENT,
  `id_salle` int(3) NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `date_depart` datetime NOT NULL,
  `prix` int(3) NOT NULL,
  `etat` enum('libre','reservation') NOT NULL,
  PRIMARY KEY (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `id_salle`, `date_arrivee`, `date_depart`, `prix`, `etat`) VALUES
(22, 4, '2018-01-16 00:00:00', '2018-01-09 00:00:00', 4000, 'libre'),
(30, 2, '2018-02-26 00:00:00', '2018-02-28 00:00:00', 800, 'libre'),
(38, 7, '2018-02-25 00:00:00', '2018-02-28 00:00:00', 4400, 'libre'),
(40, 11, '2018-03-25 00:00:00', '2018-04-30 00:00:00', 1800, 'libre'),
(42, 10, '2018-01-15 00:00:00', '2018-01-31 00:00:00', 1000, 'libre'),
(43, 12, '2018-01-03 00:00:00', '2018-01-31 00:00:00', 3400, 'libre'),
(45, 9, '2018-05-01 00:00:00', '2018-05-31 00:00:00', 5000, 'libre'),
(47, 8, '2018-05-31 00:00:00', '2018-04-26 00:00:00', 2800, 'libre'),
(48, 7, '2018-09-25 00:00:00', '2018-10-16 00:00:00', 3600, 'libre'),
(50, 5, '2018-03-07 00:00:00', '2018-03-29 00:00:00', 2050, 'libre'),
(52, 13, '2018-02-15 00:00:00', '2018-02-27 00:00:00', 3200, 'libre'),
(53, 14, '2018-04-29 00:00:00', '2018-04-26 00:00:00', 1600, 'libre'),
(54, 15, '2018-01-19 00:00:00', '2018-03-09 00:00:00', 1000, 'libre'),
(55, 16, '2018-03-01 00:00:00', '2018-04-26 00:00:00', 5000, 'libre'),
(56, 17, '2018-04-03 00:00:00', '2018-07-19 00:00:00', 4000, 'libre'),
(57, 18, '2018-06-01 00:00:00', '2018-11-13 00:00:00', 3650, 'libre'),
(58, 19, '2018-04-06 00:00:00', '2018-05-04 00:00:00', 2800, 'libre'),
(60, 21, '2018-02-27 00:00:00', '2018-03-21 00:00:00', 4400, 'libre'),
(61, 24, '2018-04-25 00:00:00', '2018-06-21 00:00:00', 5100, 'libre'),
(62, 25, '2018-04-29 00:00:00', '2018-04-19 00:00:00', 900, 'libre'),
(63, 26, '2018-02-01 00:00:00', '2018-02-02 00:00:00', 700, 'libre'),
(64, 27, '2018-01-31 21:10:00', '2018-01-26 21:10:00', 600, 'libre'),
(65, 29, '2018-04-18 21:11:00', '2018-08-07 21:12:00', 3500, 'libre'),
(66, 30, '2018-01-24 21:12:00', '2018-01-31 21:13:00', 700, 'libre'),
(67, 31, '2018-01-06 21:14:00', '2018-02-09 21:14:00', 1000, 'libre');

-- --------------------------------------------------------

--
-- Structure de la table `salle`
--

DROP TABLE IF EXISTS `salle`;
CREATE TABLE IF NOT EXISTS `salle` (
  `id_salle` int(3) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `photo` varchar(200) NOT NULL,
  `pays` varchar(20) NOT NULL,
  `ville` varchar(20) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `cp` int(5) NOT NULL,
  `capacite` int(3) NOT NULL,
  `categorie` varchar(20) NOT NULL,
  PRIMARY KEY (`id_salle`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `salle`
--

INSERT INTO `salle` (`id_salle`, `titre`, `description`, `photo`, `pays`, `ville`, `adresse`, `cp`, `capacite`, `categorie`) VALUES
(2, 'Van Gogh', 'Grande salle sera parfaite pour vos formations', '/SALLEA/photo/Van Gogh-salle8.jpg', 'France', 'Marseille', '31 rue du soleil vert', 13200, 100, 'formation'),
(4, 'Louvres', 'Cette salle sera parfaite pour vos réunions', '/SALLEA/photo/Louvres-salle7.jpg', 'France', 'Paris', '12 rue du peintre', 75001, 30, 'réunion'),
(5, 'Jouvet', 'Cette salle sera parfaite pour travailler en équipe', '/SALLEA/photo/Jouvet-bureau1.jpg', 'France', 'Lyon', '56 rue de la bohême', 69300, 30, 'bureau'),
(7, 'Chambourcy', 'Cette salle sera parfaite pour vos réunions', '/SALLEA/photo/Chambourcy-bureau2.jpg', 'France', 'Paris', '6 rue de la Vieille Gare', 75015, 50, 'bureau'),
(8, 'Coquelicot', 'Cette salle sera parfaite pour vos réunions', '/SALLEA/photo/Coquelicot-bureau3.jpg', 'France', 'Marseille', '98 rue du Port', 13900, 20, 'bureau'),
(9, 'Gabin', 'Très belle salle parfaite pour vos formations', '/SALLEA/photo/Gabin-formation1.jpg', 'France', 'Paris', '48 rue du Parrain', 75017, 20, 'formation'),
(10, 'Cosmos', 'Cette salle sera parfaite pour vos formations', '/SALLEA/photo/Cosmos-formation2.jpg', 'France', 'Lyon', '67 rue du Beaujolais', 69100, 20, 'formation'),
(11, 'Renoir', 'Salle chaleureuse parfaite pour vos réunions', '/SALLEA/photo/Renoir-reunion1.jpg', 'France', 'Lyon', '126 rue du Stade', 69400, 30, 'réunion'),
(12, 'Miro', 'Cette salle sera parfaite pour vos réunions', '/SALLEA/photo/Miro-reunion2.jpg', 'France', 'Marseille', '5 rue de la Sardine', 13600, 30, 'réunion'),
(13, 'Rousseau', 'Grande salle de formation avec écran cinéma', '/SALLEA/photo/rousseau-rousseau.jpg', 'France', 'Lyon', '168 rue des Lumières', 69500, 100, 'formation'),
(14, 'Voltaire', 'Salle de formation informatisée', '/SALLEA/photo/Voltaire-voltaire.JPG', 'France', 'Lyon', '14 rue Soliman Pacha', 69700, 20, 'formation'),
(15, 'Diderot', 'Très belle salle de formation ', '/SALLEA/photo/Diderot-diderot.JPG', 'France', 'Marseille', '42 rue de la Bouillabaisse', 13200, 20, 'formation'),
(16, 'Montesquieu', 'Très belle salle de formation ', '/SALLEA/photo/Montesquieu-montesquieu.jpg', 'France', 'Marseille', '23 rue de la Mer', 13900, 30, 'formation'),
(17, 'Flaubert', 'Salle moderne toute équipée pour formation ', '/SALLEA/photo/Flaubert-flaubert.JPG', 'France', 'Paris', '11 rue du Sahara', 75013, 30, 'formation'),
(18, 'Zola', 'Salle de formation pédagogique', '/SALLEA/photo/Zola-zola.JPG', 'France', 'Paris', '45 rue des Tropiques', 75019, 20, 'formation'),
(19, 'Chaplin', 'Très belle salle de réunion tout confort', '/SALLEA/photo/Chaplin-chaplin.jpg', 'France', 'Lyon', '123 rue de la Tour', 69850, 20, 'réunion'),
(20, 'Magritte', 'Très belle salle de réunion lumineuse', '/SALLEA/photo/Magritte-magritte.jpg', 'France', 'Lyon', '50 rue du Temple', 69200, 40, 'réunion'),
(21, 'Picasso 3', 'Très belle salle de réunion, décoration bois', '/SALLEA/photo/Picasso-picasso.jpg', 'France', 'Marseille', '101 rue du Festival', 13350, 30, 'réunion'),
(24, 'Rabelais', 'Salle de réunion, simple et convivial', '/SALLEA/photo/Rabelais-rabelais.JPG', 'France', 'Marseille', '77 rue de la Lavande', 13860, 20, 'réunion'),
(25, 'Baudelaire', 'Salle conviviale pour vos réunions', '/SALLEA/photo/Baudelaire-baudelaire.jpg', 'Ffrance', 'Paris', '58 rue de la Seine', 75005, 20, 'réunion'),
(26, 'Rimbaud', 'Salle très confortable pour vos réunions', '/SALLEA/photo/Rimbaud-rimbaud.jpg', 'France', 'Paris', '23 rue Ravachol', 75013, 20, 'réunion'),
(27, 'Camus', 'Salle de bureau simple et lumineuse', '/SALLEA/photo/Camus-camus.jpeg', 'France', 'Lyon', '85 rue de la Liberté', 69520, 5, 'bureau'),
(29, 'Brassens', 'Très belle salle de bureau ultra moderne', '/SALLEA/photo/Brassens-brassens.jpg', 'France', 'Lyon', '46 rue de la Taverne', 69700, 10, 'réunion'),
(30, 'Verlaine', 'Salle de réunion style studio photo', '/SALLEA/photo/Verlaine-verlaine.jpg', 'France', 'Marseille', '10 rue de la Plage', 13700, 20, 'réunion'),
(32, 'Clovis', 'Salle de réunion pour travailler en groupe', '/lokisalle/photo/Clovis-clovis.jpg', 'France', 'Paris', '152 rue du Palais', 75001, 30, 'réunion'),
(33, 'Gaugin', 'Très belle salle parfaite pour vos réunions', '/lokisalle/photo/Gaugin-gaugin.jpg', 'France', 'Paris', '5 rue du Paradis', 75011, 30, 'réunion');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
