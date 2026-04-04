<?php

session_start();
$message_erreur = "";
$message_erreur1 = "";
$nom = $prenom = $dateNaissance = $adresse = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = htmlspecialchars($_POST['email']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $dateNaissance = htmlspecialchars($_POST['date_naissance']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $password1 = htmlspecialchars($_POST['password1']);
    $password2 = htmlspecialchars($_POST['password2']);


    $fichier_json = 'data/utilisateurs.json';
    $utilisateurs = [];
    if (file_exists($fichier_json)) {
        $json_data = file_get_contents($fichier_json);
        $utilisateurs = json_decode($json_data, true) ?? [];
    }

    $email_existe = false;
    foreach ($utilisateurs as $user) {
        if ($user['email'] === $email) {
            $email_existe = true;
            break;
        }
    }

    if ($email_existe) {
        $message_erreur = "Cette email est déjà utilisé. Veuillez en choisir une autre.";
    } elseif ($password1!==$password2) { 
        $message_erreur1 = "Les mots de passe ne sont pas identiques";
    } else {
        $nouvel_id = count($utilisateurs) > 0 ? max(array_column($utilisateurs, 'id')) + 1 : 1;
        
        $nouvel_utilisateur = [
            "id" => $nouvel_id,
            "email" => $email,
            "mot_de_passe" => $password1 , // Sécurisation du mot de passe password_hash($password1, PASSWORD_DEFAULT)
            "role" => "client",
            "informations" => [
                "nom" => $nom,
                "prenom" => $prenom,
                "naissance" => $dateNaissance,
                "adresse" => $adresse   
            ],
            "dates" => [
                "inscription" => date("Y-m-d"),
                "derniere_connexion" => date("Y-m-d")
            ],
            "statut" => "Standard",
            "niveau de remise" => 0,
            "droit" => "normal"
        ];

        $utilisateurs[] = $nouvel_utilisateur;
        file_put_contents($fichier_json, json_encode($utilisateurs, JSON_PRETTY_PRINT));

        $_SESSION['id'] = $nouvel_utilisateur['id'];
        $_SESSION['email'] = $nouvel_utilisateur['email'];
        $_SESSION['role'] = $nouvel_utilisateur['role'];
        $_SESSION['nom'] = $nouvel_utilisateur['informations']['prenom'] . " " . $nouvel_utilisateur['informations']['nom'];

        $_SESSION['flash_message'] = "Inscription réussie ! Bienvenue parmi nous.";
        header("Location: index.php");
        exit();
        
        $message_succes = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    }
}
?>

<?php
$titre_page = "Inscriptions - Le Groin de Folie";
include 'includes/header.php';
?>

<section class="place-cadre">
    <div class="cadre">
        <h2>Inscription</h2>
        <form method="POST">
            <div class="formulaire">
                <label>Nom</label>
                <input type="text" name="nom"placeholder="Nom"  value="<?php echo $nom ?>" >
            
            <div class="formulaire">
                <label>Prenom</label>
                <input type="text" name="prenom"placeholder="Prenom"  value="<?php echo $prenom ?>" >
            </div>
            <div class="formulaire">
                <label>Date de naissance</label>
                <input type="date" name="date_naissance"placeholder="Date de naissance"  value="<?php echo $dateNaissance ?>">
            </div>
            <div class="formulaire">
                <label>Adresse</label>
                <input type="text" name="adresse" placeholder="Adresse"  value="<?php echo $adresse ?>" >
            </div>
            </div>
            <div class="formulaire">
                <label>Email</label>
                <input type="email" name="email" placeholder="Votre email"  value="<?php echo $email ?>" >
                <p style="color: #ff4d4d; font-weight: bold; text-align: center;"><?php echo $message_erreur; ?></p>
            </div>
            <div class="formulaire">
                <label>Mot de passe</label>
                <input type="password" name="password1" placeholder="Créer un mot de passe">
            </div>
            <div class="formulaire">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="password2" placeholder="Confirmer le mot de passe">
                <p style="color: #ff4d4d; font-weight: bold; text-align: center;"><?php echo $message_erreur1; ?></p>
            </div>
            <button type="submit">Créer mon compte</button>
        </form>
        <?php if ($message_erreur): ?>
<?php endif; ?>

<?php if (isset($message_succes)): ?>
    <p style="color: #2ecc71; font-weight: bold; text-align: center;"><?php echo $message_succes; ?></p>
<?php endif; ?>
        <p class="lien">
            Déjà inscrit ? <a href="connexion.html">Se connecter</a>
        </p>
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
