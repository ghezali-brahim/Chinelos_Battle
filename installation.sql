-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 09 Janvier 2015 à 22:49
-- Version du serveur :  5.6.20
-- Version de PHP :  5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `test`
--

-- --------------------------------------------------------

--
-- Structure de la table `attaque`
--

CREATE TABLE IF NOT EXISTS `attaque` (
  `id_attaque` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `degats` int(11) NOT NULL,
  `mp_used` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `attaque`
--

INSERT INTO `attaque` (`id_attaque`, `nom`, `degats`, `mp_used`) VALUES
(1, 'attaque1', 10, 1),
(2, 'attaque2', 50, 6),
(3, 'attaque3', 5, 0);

-- --------------------------------------------------------

--
-- Structure de la table `element`
--

CREATE TABLE IF NOT EXISTS `element` (
  `id_element` int(11) NOT NULL,
  `nom` varchar(64) NOT NULL,
  `id_faible_contre` varchar(64) DEFAULT NULL,
  `id_fort_contre` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `element`
--

INSERT INTO `element` (`id_element`, `nom`, `id_faible_contre`, `id_fort_contre`) VALUES
(1, 'Normal', '', NULL),
(2, 'Feu', '3', '4'),
(3, 'Eau', '4', '2'),
(4, 'Plante', '2', '3');

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

CREATE TABLE IF NOT EXISTS `equipe` (
`id_equipe` bigint(20) unsigned NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `equipe`
--

INSERT INTO `equipe` (`id_equipe`, `id_user`) VALUES
(1, 1),
(2, 1),
(3, 6),
(4, 6),
(12, 2),
(13, 2);

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `id_item` int(11) NOT NULL,
  `nom` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `prix_achat` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `actions` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `item`
--

INSERT INTO `item` (`id_item`, `nom`, `description`, `prix_achat`, `item_type`, `actions`) VALUES
(1, 'Potion HP', 'permet de monter ses pvs', 1, 1, ''),
(2, 'Potion MP', 'permet de modifier ses points de mp', 1, 1, '');

-- --------------------------------------------------------

--
-- Structure de la table `niveau`
--

CREATE TABLE IF NOT EXISTS `niveau` (
  `niveau` int(11) NOT NULL,
  `experience` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `niveau`
--

INSERT INTO `niveau` (`niveau`, `experience`) VALUES
(1, 0),
(2, 30),
(3, 60),
(4, 120),
(5, 240),
(6, 480),
(7, 800),
(8, 1200),
(9, 1800),
(10, 2800),
(11, 4000),
(12, 8000);

-- --------------------------------------------------------

--
-- Structure de la table `personnage`
--

CREATE TABLE IF NOT EXISTS `personnage` (
`id_personnage` bigint(20) unsigned NOT NULL,
  `nom` varchar(255) NOT NULL,
  `element` int(11) DEFAULT NULL,
  `niveau` int(11) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `attaques` varchar(255) DEFAULT NULL,
  `hp` int(11) DEFAULT NULL,
  `hp_max` int(11) DEFAULT NULL,
  `mp` int(11) DEFAULT NULL,
  `mp_max` int(11) DEFAULT NULL,
  `puissance` int(11) DEFAULT NULL,
  `defense` int(11) DEFAULT NULL,
  `id_equipe` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Contenu de la table `personnage`
--

INSERT INTO `personnage` (`id_personnage`, `nom`, `element`, `niveau`, `experience`, `attaques`, `hp`, `hp_max`, `mp`, `mp_max`, `puissance`, `defense`, `id_equipe`) VALUES
(1, 'Caporal', 3, 11, 7304, '1;2;3', 3, 180, 35, 65, 36, 11, 1),
(3, 'Chausson 3', 1, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 0),
(2, 'Chausson 2', 1, 1, 1, '1;2;3', 1, 10, 0, 5, 3, 1, 3),
(4, 'Chausson 4', 2, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 12),
(5, 'Chausson 5', 3, 2, 46, '1;2;3', 0, 20, 7, 10, 6, 2, 2),
(6, 'Chausson 6', 2, 1, 16, '1;2;3', 0, 10, 4, 5, 3, 1, 2),
(7, 'Chausson 7', 4, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id_user` bigint(20) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `argent` int(11) DEFAULT NULL,
  `id_equipe` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `argent`, `id_equipe`) VALUES
(1, 'admin', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'admin@admin.com', 692, 0),
(2, 'brahim', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'azerty@hotmail.fr', 100, 0),
(3, 'lolilol', 'c0cd4f939a5d41f3c89aa5b74e92d2b64ab6a2b0', 'azertylol@hotmail.fr', 10, 0),
(4, 'blabla', 'bb21158c733229347bd4e681891e213d94c685be', 'blabla@free.fr', 10, 0),
(5, 'freezing', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'freezing@free.fr', 10, 0),
(6, 'lol', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'brahim@hotmail.fr', 10, 0);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `attaque`
--
ALTER TABLE `attaque`
 ADD PRIMARY KEY (`id_attaque`);

--
-- Index pour la table `element`
--
ALTER TABLE `element`
 ADD PRIMARY KEY (`id_element`), ADD UNIQUE KEY `unique_nom` (`nom`);

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
 ADD UNIQUE KEY `id_equipe` (`id_equipe`);

--
-- Index pour la table `item`
--
ALTER TABLE `item`
 ADD PRIMARY KEY (`id_item`), ADD UNIQUE KEY `unique_nom` (`nom`);

--
-- Index pour la table `niveau`
--
ALTER TABLE `niveau`
 ADD PRIMARY KEY (`niveau`), ADD UNIQUE KEY `unique_experience` (`experience`);

--
-- Index pour la table `personnage`
--
ALTER TABLE `personnage`
 ADD UNIQUE KEY `id_personnage` (`id_personnage`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
 ADD UNIQUE KEY `id_user` (`id_user`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
MODIFY `id_equipe` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
MODIFY `id_personnage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
