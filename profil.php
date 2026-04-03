<?php
session_start();

$fichier_json = 'data/utilisateurs.json';
$utilisateurs = json_decode(file_get_contents('data/utilisateurs.json'), true);

$mon_id = $_SESSION['id']; 
$mon_profil = null;

foreach ($utilisateurs as $user) {
    if ($user['id'] == $mon_id) {
        $mon_profil = $user;
        break;
    }
}

if (isset($_POST['deco'])) { 
    session_destroy();   
    header("Location: index.php");
    exit();
}
 ?>

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
        <h2>Mon Profil</h2>

        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Nom</strong></td>
                    <td><?php echo strtoupper(htmlspecialchars($mon_profil['informations']['nom'])); ?></td>
                    <td><button>Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Prénom</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['informations']['prenom']); ?></td>
                    <td><button>Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['email']); ?></td>
                    <td><button>Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Naissance</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['informations']['naissance']); ?></td>
                    <td><button >Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Adresse</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['informations']['adresse']); ?></td>
                    <td><button >Modifier</button></td>
                </tr>

                <tr>
                    <td><strong>Rôle</strong></td>
                    <td><?php echo ucfirst(htmlspecialchars($mon_profil['role'])); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Statut</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['statut']); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Remise</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['niveau de remise']); ?> %</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Inscription</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['dates']['inscription']); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Dernière Connexion</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['dates']['derniere_connexion']); ?></td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>

        <form method="POST" style="margin-top: 30px; text-align: center;">
            <button type="submit" name="deco" class="btn-deco">Se déconnecter</button>
        </form>
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