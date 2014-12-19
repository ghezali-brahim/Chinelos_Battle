-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 28 Octobre 2014 à 22:18
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
  `pm_used` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `attaque`
--

INSERT INTO `attaque` (`id_attaque`, `nom`, `degats`, `pm_used`) VALUES
(1, 'attaque1', 10, 1),
(2, 'attaque2', 50, 6),
(3, 'attaque3', 5, 0);

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
-- Structure de la table `niveau`
--

CREATE TABLE IF NOT EXISTS `niveau` (
  `niveau` int(11) NOT NULL,
  `experience` bigint(20) NOT NULL,
  `hp_max` int(11) NOT NULL,
  `mp_max` int(11) NOT NULL,
  `puissance` int(11) NOT NULL,
  `defense` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `niveau`
--

INSERT INTO `niveau` (`niveau`, `experience`, `hp_max`, `mp_max`, `puissance`, `defense`) VALUES
(1, 0, 5, 3, 2, 0),
(2, 10, 10, 5, 3, 1),
(3, 30, 15, 7, 4, 2),
(4, 60, 20, 9, 5, 3),
(5, 120, 25, 11, 6, 4),
(6, 240, 30, 13, 7, 5),
(7, 480, 35, 15, 9, 6),
(8, 1000, 40, 20, 10, 7);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `personnage`
--

INSERT INTO `personnage` (`id_personnage`, `nom`, `element`, `niveau`, `experience`, `attaques`, `hp`, `hp_max`, `mp`, `mp_max`, `puissance`, `defense`, `id_equipe`) VALUES
(1, 'Caporal', 0, 7, 543, '1;2;3', 10, 185, 10, 185, 185, 185, 1),
(2, 'Monstre', 0, 1, 0, '2;3', 5, 5, 3, 3, 2, 0, 1),
(3, 'Monstre', 0, 1, 0, '1;2', 5, 5, 3, 3, 2, 0, 1),
(4, 'Monstre', 0, 2, 10, '1;2', 10, 10, 5, 5, 3, 1, NULL),
(5, 'Monstre', 0, 2, 10, '1;2', 10, 10, 5, 5, 3, 1, 2),
(6, 'Monstre', 0, 2, 10, '1;3', 10, 10, 5, 5, 3, 1, 1),
(7, 'Monstre', 0, 2, 10, '1;3', 10, 10, 5, 5, 3, 1, NULL),
(8, 'Monstre', 0, 2, 10, '1;2;3', 10, 10, 5, 5, 3, 1, NULL),
(9, 'Monstre', 0, 3, 30, '1;2;3', 15, 15, 7, 7, 4, 2, NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `argent`, `id_equipe`) VALUES
(1, 'admin', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'admin@admin.com', 10, 0),
(2, 'brahim', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'azerty@hotmail.fr', 100, 0),
(3, 'lolilol', 'c0cd4f939a5d41f3c89aa5b74e92d2b64ab6a2b0', 'azertylol@hotmail.fr', 10, 0),
(4, 'blabla', 'bb21158c733229347bd4e681891e213d94c685be', 'blabla@free.fr', 10, 0),
(5, 'freezing', '9cf95dacd226dcf43da376cdb6cbba7035218921', 'freezing@free.fr', 10, 0);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `attaque`
--
ALTER TABLE `attaque`
 ADD PRIMARY KEY (`id_attaque`);

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
 ADD UNIQUE KEY `id_equipe` (`id_equipe`);

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
MODIFY `id_equipe` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
MODIFY `id_personnage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
