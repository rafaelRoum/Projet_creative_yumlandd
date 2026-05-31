<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['id'])) {
    $fichier_utilisateurs_check = 'data/utilisateurs.json';
    
    if (file_exists($fichier_utilisateurs_check)) {
        $data_users = json_decode(file_get_contents($fichier_utilisateurs_check), true) ?? [];
        
        foreach ($data_users as $u) {
            if ($u['id'] == $_SESSION['id'] && $u['droit'] === 'bloquer') {
                session_destroy(); 
                header("Location: index.php?erreur=bloque"); 
                exit();
            }
        }
    }
}

$themeActuel = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'sombre') ? 'sombre.css' : 'style.css';
$iconActuel = ($themeActuel === 'sombre.css') ? 'images/groin_de_folie_icons-inverser.png' : 'images/groin_de_folie_icons.png';
$texteBouton = ($themeActuel === 'sombre.css') ? '☀️' : '🌙';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($titre_page) ? $titre_page : "Le Groin de Folie"; ?></title>
    <link rel="stylesheet" href="<?php echo $themeActuel; ?>" id="style">
    <link id="icon" rel="icon" type="image/png" href="<?php echo $iconActuel; ?>">
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
            <a href="administrateur.php">Admin</a>
            <?php
            $nombre_articles_panier = 0;
            if (isset($_SESSION['panier'])) {
                $nombre_articles_panier = array_sum($_SESSION['panier']);
            }
            ?>
            <a href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a>

        <?php elseif ($_SESSION['role'] === 'restaurateur'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="commande.php">Commandes</a>
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
                $nombre_articles_panier = array_sum($_SESSION['panier']);
            }
            ?>
            <a href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a>
        <?php endif; ?>

        <div class="theme-change">
            <button id="theme" onclick="basculerTheme()"><?php echo $texteBouton; ?></button>
        </div>
    </nav>
</header>

<script>
function basculerTheme() {
    let lienCss = document.getElementById('style');
    let bouton = document.getElementById('theme');
    let icon = document.getElementById('icon'); 
    let nouveauTheme = "";

    if (lienCss.getAttribute("href") === "style.css") {
        nouveauTheme = "sombre";
        lienCss.href = "sombre.css";
        bouton.innerHTML = "☀️"; 
        if (icon) icon.href = "images/groin_de_folie_icons-inverser.png"; 
    } else {
        nouveauTheme = "clair";
        lienCss.href = "style.css";
        bouton.innerHTML = "🌙"; 
        if (icon) icon.href = "images/groin_de_folie_icons.png";
    }

    document.cookie = "theme=" + nouveauTheme + "; max-age=" + (30*24*60*60) + "; path=/";
}
</script>