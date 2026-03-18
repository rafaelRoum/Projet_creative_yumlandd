<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Administration - Le Groin de Folie</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="groin_de_folie_icons.png">
</head>

<body>

<div class="fond">

<header class="top-menu">
    <nav>
        <a href="index.html">Accueil</a>
        <a href="presentation.html">Présentation</a>
        <a href="connexion.html">Connexion</a>
        <a href="inscription.html">Inscription</a>
        <a href="profil.html">Profil</a>
        <a href="commande.html">Commande</a>
        <a href="livraison.html">Livraison</a>
        <a href="notation.html">Notation</a>
        <a href="administrateur.html">Admin</a>
    </nav>
</header>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2>Liste de tous les Utilisateurs</h2>            
        <p style="text-align: center; color: #757575; font-size: 14px; margin-bottom: 20px;">
            <em>Les filtres de tri seront disponibles en phases 2/3</em>
        </p>
        <table class="tab-utilisateur">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jean Dupont</td>
                    <td>jean.dupont@example.com</td>
                    <td><span class="statut-badge statut-commande">A commandé</span></td>
                    <td><a href="" class="voir-profil-btn">Voir Profil</a></td>
                </tr>
                <tr>
                    <td>Marie Lecoq</td>
                    <td>marie.l@example.com</td>
                    <td><span class="statut-badge statut-vide">Aucune commande</span></td>
                    <td><a href="" class="voir-profil-btn">Voir Profil</a></td>
                </tr>
                <tr>
                    <td>Pierre Durant</td>
                    <td>p.durant@example.com</td>
                    <td><span class="statut-badge statut-commande">A commandé</span></td>
                    <td><a href="" class="voir-profil-btn">Voir Profil</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

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