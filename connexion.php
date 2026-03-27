<?php
session_start();

$message_erreur = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_saisi = $_POST['email'] ?? ''; 
    $mdp_saisi = $_POST['password'] ?? '';

    $fichier_json = 'data/utilisateurs.json';
    $utilisateurs = [];
    
    if (file_exists($fichier_json)) {
        $json_data = file_get_contents($fichier_json);
        $utilisateurs = json_decode($json_data, true) ?? [];
    }

    $utilisateur_trouve = null;


    foreach ($utilisateurs as $index => $user) {
        if (isset($user['email']) && $user['email'] === $email_saisi) {
  
            if ($mdp_saisi === $user['mot_de_passe']) {
                $utilisateur_trouve = $user;
                

                $utilisateurs[$index]['dates']['derniere_connexion'] = date("Y-m-d H:i:s");
                file_put_contents($fichier_json, json_encode($utilisateurs, JSON_PRETTY_PRINT));
                break;
            }
        }
    }

    if ($utilisateur_trouve) {
        $_SESSION['utilisateur_id'] = $utilisateur_trouve['id'];
        $_SESSION['utilisateur_email'] = $utilisateur_trouve['email'];
        $_SESSION['utilisateur_role'] = $utilisateur_trouve['role'];
        $_SESSION['utilisateur_nom'] = $utilisateur_trouve['informations']['prenom'] . " " . $utilisateur_trouve['informations']['nom'];

        header("Location: index.php");
        exit();
    } else {
        $message_erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion - Le Groin de Folie</title>
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

    <section class="place-cadre">
        <div class="cadre">
            <h2>Connexion</h2>

            <?php if ($message_erreur): ?>
                <p style="color: #ff4d4d; font-weight: bold; text-align: center;"><?php echo $message_erreur; ?></p>
            <?php endif; ?>

            <form method="POST" action="connexion.php">
                <div class="formulaire">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Votre email" required>
                </div>
                <div class="formulaire">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Votre mot de passe" required>
                </div>
                <button type="submit">Se connecter</button>
            </form>
            
            <p class="lien">
                Pas encore inscrit ? <a href="inscription.php">S'inscrire</a>
            </p>
        </div>
    </section>

    <footer>
        <div class="footer-fond">
            <div class="footer-col">
               <h3>Navigation</h3>
               <a href="index.php">Accueil</a>
               <a href="presentation.php">Présentation</a>
               <a href="connexion.php">Connexion</a>
               <a href="inscription.php">Inscription</a>
               <a href="profil.php">Profil</a>
            </div>
            <div class="footer-col">
               <h3>&nbsp</h3>
               <a href="commande.php">Commande</a>
               <a href="livraison.php">Livraison</a>
               <a href="notation.php">Notation</a>
               <a href="administrateur.php">Admin</a>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
               <p>📍 12 rue du Jambon, Paris</p>
               <p>📞 01 23 45 67 89</p>
               <p>✉️ contact@groindefolie.com</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>