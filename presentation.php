<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title> Présentation</title>
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
    
<div class="recherche-placement">
    <form class="barre-recherche center-grid">
        <input type="text" placeholder="Rechercher un plat, une envie..." />
        <button type="submit">Rechercher</button>
    </form>
</div>

<div class="categorie-placement">
    <span class="categorie-badge">Entrées / Apéro</span>
</div>

<div class="ligne-menu" >
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/charcuterie.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Charcuterie</div>
            <div class="menu-prix">9 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/fois_gras.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Foie gras</div>
            <div class="menu-prix">12 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/oeuf_mimosa.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Oeufs mimosa</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/gougere.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Gougères</div>
            <div class="menu-prix">7 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/frommage.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Frommage</div>
            <div class="menu-prix">9 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/salade.png')"></div>
        <div class="menu-content">
            <div class="menu-titre">Salade </div>
            <div class="menu-prix">12 €</div>
        </div>
    </div>
        <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/saumon_fume.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Saumon fumé</div>
            <div class="menu-prix">12 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/chevre_chaud.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Chèvres chaud</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/quiche.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Quiche</div>
            <div class="menu-prix">9 €</div>
        </div>
    </div>
</div>

<div class="categorie-placement">
    <span class="categorie-badge">Plats</span>
</div>

<div class="ligne-menu">
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/roti_porc.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Rôti de porc</div>
            <div class="menu-prix">23 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/filetmignon.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Filet mignon</div>
            <div class="menu-prix">21 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/tartare.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">tartare de boeuf</div>
            <div class="menu-prix">19 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/entrecote.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Entrecôte</div>
            <div class="menu-prix">25 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/pate_carbo.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Pâtes carbonara</div>
            <div class="menu-prix">17 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/saucisse_lentille.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Saucisse lentille</div>
            <div class="menu-prix">19 €</div>
        </div>
    </div>
        <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/poulet_roti.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Poulet rôti</div>
            <div class="menu-prix">18 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/big_groin.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">big groin</div>
            <div class="menu-prix">18 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/ribs_porc.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Ribs de porc</div>
            <div class="menu-prix">22 €</div>
        </div>
    </div>
</div>

<div class="categorie-placement">
    <span class="categorie-badge">Accompagnements</span>
</div>

<div class="ligne-menu">
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/frite.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Frites</div>
            <div class="menu-prix">5 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/potatoes.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Potatoes</div>
            <div class="menu-prix">5 €</div>
        </div>
    </div>
</div>

<div class="categorie-placement">
    <span class="categorie-badge">Desserts</span>
</div>

<div class="ligne-menu">
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/fondant.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Fondant</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/tiramisu.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Tiramisu</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/cheesecake.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Cheesecake</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/creme_brule.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Crème brulée</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/tarte_tatin.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Tarte Tatin</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/millefeuille.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Mille Feuille</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
        <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/paris_brest.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Paris Brest</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/ile_flotante.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Ile flotante</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/donut_choco.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Donut choco</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
</div>

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

