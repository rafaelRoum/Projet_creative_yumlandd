<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title> Profil</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="groin_de_folie_icons.png">
</head>

<body>

<div class="fond">

<header class="top-menu">
    <nav>
        <a href="index.php">Accueil</a>
        <a href="presentation.php">Présentation</a>
        <a href="connexion.php">Connexion</a>
        <a href="inscription.php">Inscription</a>
        <a href="profil.php">Profil</a>
        <a href="commande.php">Commande</a>
        <a href="livraison.php">Livraison</a>
        <a href="notation.php">Notation</a>
        <a href="administrateur.php">Admin</a>
    </nav>
</header>


<section class="place-cadre">
    <div class="cadre">
        <h2>Mon Profil</h2>
            <div class="info-profil">
                <label>Nom :</label>
                <value id="user-name">Jean Dupont</value>
                <button>Modifier</button>
            </div>
            <div class="info-profil">
                <label>Email :</label>
                <value id="user-email">jean.dupont@example.com</value>
                <button >Modifier</button>
            </div>
            <div class="info-profil">
                <label>Téléphone :</label>
                <value id="user-phone">+33 6 12 34 56 78</value>
                <button >Modifier</button>
            </div>
        <button>Se déconnecter</button>
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