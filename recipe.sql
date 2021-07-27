-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 02 juil. 2021 à 18:38
-- Version du serveur :  8.0.23
-- Version de PHP : 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `recipe`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`, `slug`, `description`) VALUES
(1, 'Category One', NULL, 'Description Category One'),
(2, 'Category Two', NULL, 'Description Category Two');

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `auteur_id` int DEFAULT NULL,
  `main_comment_id` int DEFAULT NULL,
  `recette_id` int DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ingredient`
--

CREATE TABLE `ingredient` (
  `id` int NOT NULL,
  `recette_id` int DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite` double DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ingredient`
--

INSERT INTO `ingredient` (`id`, `recette_id`, `designation`, `quantite`, `type`) VALUES
(1, 1, 'Ingredient One', NULL, NULL),
(2, 1, 'Ingredient Two', NULL, NULL),
(3, 1, 'Ingredient Three', NULL, NULL),
(4, 1, 'Ingredient Four', NULL, NULL),
(5, 2, 'Ingredient One', NULL, NULL),
(6, 2, 'Ingredient Two', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int NOT NULL,
  `recette_id` int DEFAULT NULL,
  `type` int DEFAULT '1',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `recette_id`, `type`, `path`) VALUES
(1, 1, NULL, '60df5353822ef@1625248595.jpg'),
(2, 1, NULL, '60df535383854@1625248595.jpg'),
(3, 1, NULL, '60df53538437d@1625248595.jpg'),
(4, 1, NULL, '60df535384d89@1625248595.jpg'),
(5, 1, NULL, '60df5c477ad0f@1625250887.jpg'),
(6, 1, NULL, '60df5c477b170@1625250887.jpg'),
(7, 1, NULL, '60df5c477b4f1@1625250887.jpg'),
(8, 1, NULL, '60df5c477b840@1625250887.jpg'),
(9, 1, NULL, '60df5c477bbb4@1625250887.jpg'),
(10, 2, NULL, '60df5cb31acad@1625250995.jpg'),
(11, 2, NULL, '60df5cb31af1a@1625250995.jpg'),
(12, 2, NULL, '60df5cb31b0a0@1625250995.jpg'),
(13, 2, NULL, '60df5cb31b1f8@1625250995.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `note`
--

CREATE TABLE `note` (
  `id` int NOT NULL,
  `auteur_id` int DEFAULT NULL,
  `recette_id` int DEFAULT NULL,
  `valeur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recette`
--

CREATE TABLE `recette` (
  `id` int NOT NULL,
  `categorie_id` int DEFAULT NULL,
  `auteur_id` int DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `personne` int NOT NULL,
  `duree` int DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recette`
--

INSERT INTO `recette` (`id`, `categorie_id`, `auteur_id`, `nom`, `slug`, `description`, `image`, `difficulte`, `personne`, `duree`, `date`) VALUES
(1, 1, 1, 'Recipe One One', 'recipe-one-one', 'Description Recipe One One', '60df5c475f183.jpg', 'Moyen', 3, 2, '2021-07-02 20:34:47'),
(2, 2, 1, 'Recipe Two Two', 'recipe-two-two', 'Description Recipe Two Two', '60df5cb30a7d0.jpg', 'Moyen', 4, 2, '2021-07-02 20:36:35');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biography` longtext COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rules` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `username`, `slug`, `biography`, `avatar`, `password`, `rules`) VALUES
(1, 'denver@gmail.com', 'Carols DENVER', 'carols-denver', NULL, NULL, '$2y$13$NXY2Aj9UxlK953FwSIV/JupnCDWjmLdbA.uebcoEeSnSXiTAoC1n.', 'ROLE_ADMIN,ROLE_USER');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526C60BB6FE6` (`auteur_id`),
  ADD KEY `IDX_9474526CC0910970` (`main_comment_id`),
  ADD KEY `IDX_9474526C89312FE9` (`recette_id`);

--
-- Index pour la table `ingredient`
--
ALTER TABLE `ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6BAF787089312FE9` (`recette_id`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A2CA10C89312FE9` (`recette_id`);

--
-- Index pour la table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CFBDFA1460BB6FE6` (`auteur_id`),
  ADD KEY `IDX_CFBDFA1489312FE9` (`recette_id`);

--
-- Index pour la table `recette`
--
ALTER TABLE `recette`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_49BB6390BCF5E72D` (`categorie_id`),
  ADD KEY `IDX_49BB639060BB6FE6` (`auteur_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ingredient`
--
ALTER TABLE `ingredient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `note`
--
ALTER TABLE `note`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `recette`
--
ALTER TABLE `recette`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C60BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526C89312FE9` FOREIGN KEY (`recette_id`) REFERENCES `recette` (`id`),
  ADD CONSTRAINT `FK_9474526CC0910970` FOREIGN KEY (`main_comment_id`) REFERENCES `comment` (`id`);

--
-- Contraintes pour la table `ingredient`
--
ALTER TABLE `ingredient`
  ADD CONSTRAINT `FK_6BAF787089312FE9` FOREIGN KEY (`recette_id`) REFERENCES `recette` (`id`);

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_6A2CA10C89312FE9` FOREIGN KEY (`recette_id`) REFERENCES `recette` (`id`);

--
-- Contraintes pour la table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `FK_CFBDFA1460BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_CFBDFA1489312FE9` FOREIGN KEY (`recette_id`) REFERENCES `recette` (`id`);

--
-- Contraintes pour la table `recette`
--
ALTER TABLE `recette`
  ADD CONSTRAINT `FK_49BB639060BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_49BB6390BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
