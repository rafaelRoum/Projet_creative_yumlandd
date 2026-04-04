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
        $_SESSION['id'] = $utilisateur_trouve['id'];
        $_SESSION['email'] = $utilisateur_trouve['email'];
        $_SESSION['role'] = $utilisateur_trouve['role'];
        $_SESSION['nom'] = $utilisateur_trouve['informations']['prenom'] . " " . $utilisateur_trouve['informations']['nom'];

        header("Location: index.php");
        exit();
    } else {
        $message_erreur = "Email ou mot de passe incorrect.";
    }
}
?>


<?php
$titre_page = "Connexion - Le Groin de Folie";
include 'includes/header.php';
?>

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

<?php
include 'includes/footer.php';
?>