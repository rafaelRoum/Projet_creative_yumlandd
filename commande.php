<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Le Groin de Folie - Gestion des Commandes</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="groin_de_folie_icons.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<div class="admin-cadre-placement">
    <div class="admin-cadre" style="margin-bottom: 50px;">
        <h2 class="france-ancien-livre">Commandes à préparer</h2>
        <table class="tab-utilisateur">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Contenu</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#1024</td>
                    <td>Justin Mason</td>
                    <td>1x Quiche, 1x Fondant</td>
                    <td><span class="statut-badge" style="background: #ffebee; color: #c62828;">En cuisine</span></td>
                    <td>
                        <button onclick="alert('Commande envoyée en livraison !')" style="padding: 8px 15px; font-size: 12px; width: auto; margin: 0; background-color: #d4a017;">
                            🚀 Expédier
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>#1025</td>
                    <td>Sophie Fonte</td>
                    <td>2x Saumon Fumé</td>
                    <td><span class="statut-badge" style="background: #ffebee; color: #c62828;">En cuisine</span></td>
                    <td>
                        <button style="padding: 8px 15px; font-size: 12px; width: auto; margin: 0; background-color: #d4a017;">
                            🚀 Expédier
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">🛵 En cours de livraison</h2>
        <table class="tab-utilisateur">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Livreur</th>
                    <th>Destination</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#1020</td>
                    <td>Marc Livreur</td>
                    <td>12 rue du Jambon</td>
                    <td><span class="statut-badge" style="background: #e3f2fd; color: #1565c0;">Sur la route</span></td>
                    <td>
                        <button style="padding: 8px 15px; font-size: 12px; width: auto; margin: 0; background-color: #4f6f4f;">
                            ✅ Valider réception
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>

<footer>
    <div class="footer-fond">
        <div class="footer-col">
            <h3>Navigation</h3>
                <a href="index.html">Accueil</a>
                <a href="presentation.html">Présentation</a>
                <a href="administrateur.html">Admin</a>
            </div>
            <div class="footer-col">
                <h3>&nbsp;</h3>
                <a href="commande.html">Commandes</a>
                <a href="livraison.html">Livraison</a>
                <a href="profil.html">Profil</a>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
                <a href="#">📍 12 rue du Jambon, Paris</a>
                <a href="#">📞 01 23 45 67 89</a>
                <a href="#">✉️ contact@groindefolie.com</a>
            </div>
        </div>
    </div>
</footer>

</div>

</html>