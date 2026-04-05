<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($titre_page) ? $titre_page : "Le Groin de Folie"; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/groin_de_folie_icons.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="fond">

<header class="top-menu">
    <nav>
        <?php if (!isset($_SESSION['role'])): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php">Inscription</a>

        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php">Inscription</a>
            <a href="profil.php">Profil</a>
            <a href="commande.php">Commande</a>
            <a href="livraison.php">Livraison</a>
            <a href="notation.php">Notation</a>
            <a href="administrateur.php">Admin</a>

        <?php elseif ($_SESSION['role'] === 'restaurateur'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="commandes.php">Commandes</a>
            <a href="profil.php">Mon Profil</a>

        <?php elseif ($_SESSION['role'] === 'livreur'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="livraison.php">Livraison</a>
            <a href="profil.php">Mon Profil</a>

        <?php elseif ($_SESSION['role'] === 'client'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="profil.php">Mon Profil</a>
            <?php
            $nombre_articles_panier = 0;
            if (isset($_SESSION['panier'])) {
                $nombre_articles_panier = array_sum($_SESSION['panier']); // Additionne toutes les quantités
                }
            ?>
            <a href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a>
        <?php endif; ?>
    </nav>
</header>