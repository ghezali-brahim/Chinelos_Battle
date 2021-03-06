<?php

$reqTableUsers = "
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
(11, 4000);

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
MODIFY `id_equipe` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
MODIFY `id_personnage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
";
$connexion->query ( $reqTableUsers );

