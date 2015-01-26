-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 27 Janvier 2015 à 00:32
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
-- Structure de la table `action`
--

CREATE TABLE IF NOT EXISTS `action` (
`idAction` bigint(20) unsigned NOT NULL,
  `hp` int(11) NOT NULL,
  `mp` int(11) NOT NULL,
  `attaque` int(11) NOT NULL,
  `defense` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
-- Structure de la table `combats`
--

CREATE TABLE IF NOT EXISTS `combats` (
`id_combat` int(11) NOT NULL,
  `id_joueur_1` int(11) NOT NULL,
  `id_joueur_2` int(11) NOT NULL,
  `indice_perso_j1` int(11) NOT NULL DEFAULT '-1',
  `indice_perso_j2` int(11) NOT NULL DEFAULT '-1',
  `valider_j1` tinyint(1) NOT NULL DEFAULT '0',
  `valider_j2` tinyint(1) NOT NULL DEFAULT '0',
  `finit` tinyint(1) NOT NULL DEFAULT '0',
  `nombre_tour` int(11) NOT NULL DEFAULT '0',
  `indice_tour_de_joueur` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `combats`
--

INSERT INTO `combats` (`id_combat`, `id_joueur_1`, `id_joueur_2`, `indice_perso_j1`, `indice_perso_j2`, `valider_j1`, `valider_j2`, `finit`, `nombre_tour`, `indice_tour_de_joueur`) VALUES
(1, 1, 2, -1, -1, 0, 0, 0, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `equipe`
--

INSERT INTO `equipe` (`id_equipe`, `id_user`) VALUES
(5, 4),
(6, 4),
(7, 5),
(8, 5),
(9, 6),
(10, 6),
(11, 7),
(12, 7);

-- --------------------------------------------------------

--
-- Structure de la table `inventaire`
--

CREATE TABLE IF NOT EXISTS `inventaire` (
  `id_user` bigint(20) unsigned NOT NULL,
  `id_item` int(11) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `inventaire`
--

INSERT INTO `inventaire` (`id_user`, `id_item`, `quantite`) VALUES
(1, 1, 10);

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

CREATE TABLE IF NOT EXISTS `item` (
`id_item` bigint(20) unsigned NOT NULL,
  `nom` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `prix_achat` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `id_action` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `item`
--

INSERT INTO `item` (`id_item`, `nom`, `description`, `prix_achat`, `type`, `id_action`) VALUES
(1, 'Potion de vie', 'Redonne des points de vie', 1, 1, 1),
(2, 'Bonbon de puissance', 'augmente la puissance', 5, 2, 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `messages`
--

INSERT INTO `messages` (`id_message`, `objet`, `contenu`, `id_expeditaire`, `id_destinataire`, `date_envoie`, `lu`) VALUES
(1, 'coucou', 'sdknfuisfi sdl', 4, 5, '2015-01-26 00:00:00', 0),
(2, 'duhsuihifsd', 'sgfgsg', 5, 6, '2015-01-26 00:00:00', 0),
(3, 'jdighsdf', 'fgsgsdgsfgsd', 5, 7, '2015-01-26 10:09:00', 0),
(4, 'Coucou toi', 'salut toi ', 8, 5, '2015-01-27 00:22:00', 0),
(5, 'Coucou toi', 'salut toi ', 8, 5, '2015-01-27 00:22:00', 0);

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
(2, 10),
(3, 30),
(4, 60),
(5, 90),
(6, 120),
(7, 180),
(8, 240),
(9, 300),
(10, 500),
(11, 600),
(12, 700);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `personnage`
--

INSERT INTO `personnage` (`id_personnage`, `nom`, `element`, `niveau`, `experience`, `attaques`, `hp`, `hp_max`, `mp`, `mp_max`, `puissance`, `defense`, `id_equipe`) VALUES
(2, 'ChineloN1', 2, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 5),
(1, 'Chausson 1', 1, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 5),
(3, 'Ryanne', 3, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 5),
(4, 'Chausson 4', 2, 1, 2, '1;2;3', 0, 10, 0, 5, 3, 1, 8),
(5, 'Naboo', 4, 12, 1002, '1;2;3', 0, 20, 8, 10, 6, 2, 7),
(6, 'Gizmo', 2, 1, 500, '1;2;3', 0, 10, 4, 5, 3, 1, 7),
(7, 'Chausson 7', 1, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 9),
(8, 'Charentaise', 3, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 10),
(9, 'Sabot', 1, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 10),
(10, 'Chausson 10', 4, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 11),
(11, 'Wavy', 4, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 12),
(12, 'Pinkie', 2, 1, 0, '1;2;3', 10, 10, 5, 5, 3, 1, 12);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `last_connection`, `connected`, `argent`, `nombre_victoire`, `nombre_defaite`) VALUES
(7, 'jeremy', 'ba542f39b83af69bcd74b7e46f700ad5c8588da3', 'jeremy@jeremy.com', '2015-01-27 00:06:51', 0, 0, 0, 0),
(6, 'sarah', 'c9558dd1f7ecf229a82bb4c9fd24b080b6a96243', 'sarah@sarah.com', '2015-01-27 00:04:34', 0, 0, 0, 0),
(5, 'brahim', '6b3f0f12ce01f00d02ac4543320f85c224ef1a7d', 'brahim@brahim.com', '2015-01-27 00:28:33', 1, 4, 1, 2),
(4, 'corentin', '42a01d418fddb417c4c490cd44bb7d2a194afdfe', 'corentin@corentin.com', '2015-01-26 23:59:30', 0, 0, 0, 0),
(8, 'aurelien', '5ef421333927b81004c0be70a23f757584dc5c1d', 'aurelien@aurelien.com', '2015-01-27 00:09:42', 0, 10, 0, 0);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `action`
--
ALTER TABLE `action`
 ADD PRIMARY KEY (`idAction`), ADD UNIQUE KEY `id` (`idAction`);

--
-- Index pour la table `attaque`
--
ALTER TABLE `attaque`
 ADD PRIMARY KEY (`id_attaque`);

--
-- Index pour la table `combats`
--
ALTER TABLE `combats`
 ADD PRIMARY KEY (`id_combat`);

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
-- Index pour la table `inventaire`
--
ALTER TABLE `inventaire`
 ADD PRIMARY KEY (`id_user`), ADD UNIQUE KEY `id_inventaire` (`id_user`);

--
-- Index pour la table `item`
--
ALTER TABLE `item`
 ADD PRIMARY KEY (`id_item`), ADD UNIQUE KEY `id` (`id_item`), ADD KEY `idaction` (`id_action`), ADD KEY `index` (`id_item`,`id_action`) COMMENT 'index';

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
-- AUTO_INCREMENT pour la table `action`
--
ALTER TABLE `action`
MODIFY `idAction` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `combats`
--
ALTER TABLE `combats`
MODIFY `id_combat` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
MODIFY `id_equipe` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `item`
--
ALTER TABLE `item`
MODIFY `id_item` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
MODIFY `id_message` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
MODIFY `id_personnage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
