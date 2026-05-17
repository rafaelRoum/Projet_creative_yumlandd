<?php
session_start();

$message_erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sécurisation contre les alertes PHP avec l'opérateur '?? ""'
    $email = htmlspecialchars($_POST['email'] ?? '');
    $motDePasse1 = htmlspecialchars($_POST['mot_de_passe1'] ?? '');

    $fichier_json = 'data/utilisateurs.json';
    $utilisateurs = [];

    if (file_exists($fichier_json)) {
        $json_data = file_get_contents($fichier_json);
        $utilisateurs = json_decode($json_data, true) ?? [];
    }

    $utilisateur_trouve = null;

    foreach ($utilisateurs as $index => $user) {
        if (isset($user['email']) && $user['email'] === $email) {
            if ($motDePasse1 === $user['mot_de_passe']) {
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

        <form method="POST" id="formConnexion" novalidate>
            <div class="formulaire">
                <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre email"
                       class="<?php echo !empty($message_erreur) ? 'saisie-erreur' : ''; ?>"
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" maxlength="100">
                <div class="msg-erreur-js" id="err_email">Veuillez entrer une adresse email valide (ex: nom@domaine.com).</div>
                <span class="compteur-chars" id="cpt_email">0/100</span>
            </div>

            <div class="formulaire">
                <label for="mot_de_passe1">Mot de passe</label>
                <div class="champ-mdp">
                    <input type="password" id="mot_de_passe1" name="mot_de_passe1" placeholder="Votre mot de passe"
                           class="<?php echo !empty($message_erreur) ? 'saisie-erreur' : ''; ?>" maxlength="50">
                    <img src="images/oeil.png" class="toggle-password" alt="Afficher" onclick="basculerVisibiliteMdp('mot_de_passe1', this)">
                </div>
                <div class="msg-erreur-js" id="err_mot_de_passe1">Le mot de passe doit contenir au moins 6 caractères.</div>
                <span class="compteur-chars" id="cpt_mot_de_passe1">0/50</span>
            </div>

            <?php if ($message_erreur): ?>
                <div class="msg-erreur-js" style="display: block; text-align: center; margin-bottom: 15px;"><?php echo $message_erreur; ?></div>
            <?php endif; ?>

            <button type="submit">Se connecter</button>
        </form>
        
        <p class="lien">
            Pas encore inscrit ? <a href="inscription.php">S'inscrire</a>
        </p>
    </div>
</section>

<script>
function basculerVisibiliteMdp(idChamp, elementImage) {
    const champ = document.getElementById(idChamp);
    if (champ.type === "password") {
        champ.type = "text";
        elementImage.src = "images/cacher.png"; 
        elementImage.alt = "Masquer";
    } else {
        champ.type = "password";
        elementImage.src = "images/oeil.png";
        elementImage.alt = "Afficher";
    }
}


const champEmail = document.getElementById('email');
const champMotDePasse1 = document.getElementById('mot_de_passe1');


const modeleEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


function appliquerErreur(elementInput, idMessageErreur) {
    elementInput.classList.remove('saisie-valide');
    elementInput.classList.add('saisie-erreur');
    document.getElementById(idMessageErreur).style.display = 'block';
}

function appliquerValidation(elementInput, idMessageErreur) {
    elementInput.classList.remove('saisie-erreur');
    elementInput.classList.add('saisie-valide');
    document.getElementById(idMessageErreur).style.display = 'none';
}


function validerEmail() {
    if (!modeleEmail.test(champEmail.value.trim())) { 
        appliquerErreur(champEmail, 'err_email'); 
        return false; 
    }
    appliquerValidation(champEmail, 'err_email'); 
    return true;
}

function validerMotDePasse1() {
    if (champMotDePasse1.value.length < 6) { 
        appliquerErreur(champMotDePasse1, 'err_mot_de_passe1'); 
        return false; 
    }
    appliquerValidation(champMotDePasse1, 'err_mot_de_passe1'); 
    return true;
}


champEmail.addEventListener('input', validerEmail);
champMotDePasse1.addEventListener('input', validerMotDePasse1);

function mettreAJourCompteur(champ, idCompteur) {
    const span = document.getElementById(idCompteur);
    const max = champ.maxLength;
    const nb = champ.value.length;
    span.textContent = nb + '/' + max;
    if (nb >= max * 0.9) {
        span.classList.add('compteur-alerte');
    } else {
        span.classList.remove('compteur-alerte');
    }
}

champEmail.addEventListener('input', () => mettreAJourCompteur(champEmail, 'cpt_email'));
champMotDePasse1.addEventListener('input', () => mettreAJourCompteur(champMotDePasse1, 'cpt_mot_de_passe1'));


document.getElementById('formConnexion').addEventListener('submit', function(e) {
    const validationEmail = validerEmail();
    const validationMdp1 = validerMotDePasse1();


    if (!(validationEmail && validationMdp1)) {
        e.preventDefault();
    }
});
</script>

<?php
include 'includes/footer.php';
?>