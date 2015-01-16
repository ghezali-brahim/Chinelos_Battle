-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 16 Janvier 2015 à 21:54
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `equipe`
--

INSERT INTO `equipe` (`id_equipe`, `id_user`) VALUES
(1, 1),
(2, 1);

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
-- Structure de la table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
`id_message` bigint(20) unsigned NOT NULL,
  `objet` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `id_expeditaire` int(11) NOT NULL,
  `id_destinataire` int(11) NOT NULL,
  `date_envoie` datetime NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `personnage`
--

INSERT INTO `personnage` (`id_personnage`, `nom`, `element`, `niveau`, `experience`, `attaques`, `hp`, `hp_max`, `mp`, `mp_max`, `puissance`, `defense`, `id_equipe`) VALUES
(1, 'Chausson 1', 4, 1, 1, '1;2;3', 0, 10, 0, 5, 3, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id_user` bigint(20) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_connection` datetime DEFAULT NULL,
  `connected` tinyint(1) NOT NULL DEFAULT '0',
  `argent` int(11) DEFAULT NULL,
  `nombre_victoire` int(11) NOT NULL DEFAULT '0',
  `nombre_defaite` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `last_connection`, `connected`, `argent`, `nombre_victoire`, `nombre_defaite`) VALUES
(1, 'admin', 'c91134514349233cda39fba1da675f3031eb83ff', 'admin@iut.univ-paris8.fr', '2015-01-16 21:40:53', 1, 15, 1, 1),
(2, 'jeremyducon', 'd5188caa3f8a36e2e7172aa0914071918c90c426', 'jeremyducon@gmail.com', '0000-00-00 00:00:00', 0, 10, 0, 0);

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
-- Index pour la table `messages`
--
ALTER TABLE `messages`
 ADD PRIMARY KEY (`id_message`), ADD UNIQUE KEY `id_message` (`id_message`);

--
-- Index pour la table `niveau`
--
ALTER TABLE `niveau`
 ADD PRIMARY KEY (`niveau`), ADD UNIQUE KEY `unique_experience` (`experience`);

--
-- Index pour la table `personnage`
--
ALTER TABLE `personnage`
 ADD UNIQUE KEY `id_personnage` (`id_personnage`), ADD UNIQUE KEY `nom` (`nom`);

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
MODIFY `id_equipe` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
MODIFY `id_message` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
MODIFY `id_personnage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
