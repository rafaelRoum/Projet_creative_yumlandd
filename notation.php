<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title> Notation</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="groin_de_folie_icons.png">
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
            <a href="administrateur.php">Administrtaeur</a>
            <a href="profil.php">Mon Profil</a>

        <?php elseif ($_SESSION['role'] === 'restaurateur'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="commandes.php">Commandes à préparer</a>
            <a href="profil.php">Mon Profil</a>

        <?php elseif ($_SESSION['role'] === 'livreur'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="livraison.php">Commande en cours</a>
            <a href="profil.php">Mon Profil</a>

        <?php elseif ($_SESSION['role'] === 'client'): ?>
            <a href="index.php">Accueil</a>
            <a href="presentation.php">Présentation</a>
            <a href="profil.php">Mon Profil</a>
        <?php endif; ?>
    </nav>
</header>

<section class="place-cadre">
    <div class="cadre">
        <h2>Notation de votre commande</h2>
        <div class="formulaire">
            <label>Qualité de la livraison</label>
            <div class="etoile" data-type="livraison">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>
        </div>
        <div class="formulaire">
            <label>Qualité des produits</label>
            <div class="etoile" data-type="produits">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>
        </div>
        <div class="formulaire">
            <label for="comment">Commentaire (optionnel)</label>
            <textarea id="commentaire" placeholder="Votre avis..."></textarea>
        </div>
        <button class="envoi-notation">Envoyer ma note</button>
    </div>
</section>

</body> 

<footer>
    <div class="footer-fond">
        <div class="footer-col">
           <h3>Navigation</h3>
           <a href="index.html">Accueil</a>
           <a href="presentation.html">Présentation</a>
           <a href="connexion.html">Connexion</a>
           <a href="inscription.html">Inscription</a>
           <a href="profil.html">Profil</a>
        </div>
        <div class="footer-col">
           <h3>&nbsp</h3>
           <a href="commande.html">Commande</a>
           <a href="livraison.html">Livraison</a>
           <a href="notation.html">Notation</a>
           <a href="administrateur.html">Admin</a>
        </div>
        <div class="footer-col">
            <h3>Contact</h3>
           <a href="">📍 12 rue du Jambon, Paris</a>
           <a href="">📞 01 23 45 67 89</a>
           <a href="">✉️ contact@groindefolie.com</a>
        </div>
    </div>
</footer>

</div>

</html>