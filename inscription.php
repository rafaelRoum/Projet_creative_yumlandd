<?php
require_once 'includes/fonctions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message_erreur = "";
$nom = $prenom = $dateNaissance = $adresse = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verifier_token_csrf();


    $recaptcha_response = $_POST['g-recaptcha-response'] ?? "";
    $cle_secrete = "6LdV_xMtAAAAAKeAg3JjfF6F1cNd-Qb1Idy7FEZY"; 
    
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $cle_secrete . "&response=" . $recaptcha_response;
    $verification = @file_get_contents($url);
    $reponse_json = json_decode($verification, true);

    if (!$reponse_json || !isset($reponse_json['success']) || !$reponse_json['success']) {
        $message_erreur = "Veuillez cocher la case 'Je ne suis pas un robot'.";
    }

    if (empty($message_erreur)) {
        $email = htmlspecialchars($_POST['email'] ?? "");
        $nom = htmlspecialchars($_POST['nom'] ?? "");
        $prenom = htmlspecialchars($_POST['prenom'] ?? "");
        $dateNaissance = htmlspecialchars($_POST['date_naissance'] ?? "");
        $adresse = htmlspecialchars($_POST['adresse'] ?? "");
        $motDePasse1 = $_POST['mot_de_passe1'] ?? "";
        $motDePasse2 = $_POST['mot_de_passe2'] ?? "";

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
            $message_erreur = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            $nouvel_id = count($utilisateurs) > 0 ? max(array_column($utilisateurs, 'id')) + 1 : 1;
            
            $nouvel_utilisateur = [
                "id" => $nouvel_id,
                "email" => $email,
                "mot_de_passe" => password_hash($motDePasse1, PASSWORD_DEFAULT),
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
        }
    }
}


$titre_page = "Inscriptions - Le Groin de Folie";
include 'includes/header.php';
?>

<section class="place-cadre">
    <div class="cadre">
        <h2>Inscription</h2>
        <form method="POST" id="formInscription" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">
            
            <div class="formulaire">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo $nom ?>" maxlength="50">
                <div class="msg-erreur-js" id="err_nom">Veuillez renseigner votre nom (lettres uniquement).</div>
                <span class="compteur-chars" id="cpt_nom">0/50</span>
            </div>
            
            <div class="formulaire">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo $prenom ?>" maxlength="50">
                <div class="msg-erreur-js" id="err_prenom">Veuillez renseigner votre prénom (lettres uniquement).</div>
                <span class="compteur-chars" id="cpt_prenom">0/50</span>
            </div>
            
            <div class="formulaire">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $dateNaissance ?>">
                <div class="msg-erreur-js" id="err_date_naissance">Vous devez avoir au moins 13 ans pour vous inscrire.</div>
            </div>
            
            <div class="formulaire">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" placeholder="Adresse" value="<?php echo $adresse ?>" maxlength="100">
                <div class="msg-erreur-js" id="err_adresse">Veuillez renseigner une adresse valide (min. 6 caractères).</div>
                <span class="compteur-chars" id="cpt_adresse">0/100</span>
            </div>
            
            <div class="formulaire">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Votre email"
                       class="<?php echo !empty($message_erreur) ? 'saisie-erreur' : ''; ?>"
                       value="<?php echo $email ?>" maxlength="100">
                <div class="msg-erreur-js" id="err_email">Veuillez entrer une adresse email valide (ex: nom@domaine.com).</div>
                <span class="compteur-chars" id="cpt_email">0/100</span>
                
                <?php if ($message_erreur && strpos($message_erreur, 'email') !== false): ?>
                    <div class="msg-erreur-js" style="display: block;"><?php echo $message_erreur; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="formulaire">
                <label for="mot_de_passe1">Mot de passe</label>
                <div class="champ-mdp">
                    <input type="password" id="mot_de_passe1" name="mot_de_passe1" placeholder="Créer un mot de passe" maxlength="50">
                    <img src="images/oeil.png" class="toggle-password" alt="Afficher" onclick="basculerVisibiliteMdp('mot_de_passe1', this)">
                </div>
                <div class="msg-erreur-js" id="err_mot_de_passe1">Le mot de passe doit contenir au moins 6 caractères.</div>
                <span class="compteur-chars" id="cpt_mot_de_passe1">0/50</span>
            </div>
            
            <div class="formulaire">
                <label for="mot_de_passe2">Confirmer le mot de passe</label>
                <div class="champ-mdp">
                    <input type="password" id="mot_de_passe2" name="mot_de_passe2" placeholder="Confirmer le mot de passe" maxlength="50">
                    <img src="images/oeil.png" class="toggle-password" alt="Afficher" onclick="basculerVisibiliteMdp('mot_de_passe2', this)">
                </div>
                <div class="msg-erreur-js" id="err_mot_de_passe2">Les deux mots de passe ne correspondent pas.</div>
                <span class="compteur-chars" id="cpt_mot_de_passe2">0/50</span>
            </div>

            <div class="formulaire" style="display: flex; flex-direction: column; align-items: center; margin: 15px 0;">
                <div class="g-recaptcha" data-sitekey="6LdV_xMtAAAAAIYMCZiUU93UClx2pBb-X4N8iT4-"></div>
                <?php if ($message_erreur && strpos($message_erreur, 'robot') !== false): ?>
                    <div class="msg-erreur-js" style="display: block; text-align: center;"><?php echo $message_erreur; ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit">Créer mon compte</button>
        </form>

        <p class="lien">
            Déjà inscrit ? <a href="connexion.php">Se connecter</a>
        </p>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

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

const champNom = document.getElementById('nom');
const champPrenom = document.getElementById('prenom');
const champDateNaissance = document.getElementById('date_naissance');
const champAdresse = document.getElementById('adresse');
const champEmail = document.getElementById('email');
const champMotDePasse1 = document.getElementById('mot_de_passe1');
const champMotDePasse2 = document.getElementById('mot_de_passe2');

const modeleLettres = /^[a-zA-ZàâäéèêëîïôöùûüçÇÉÈÀ -]{1,50}$/;
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

function validerNom() {
    if (!modeleLettres.test(champNom.value.trim())) { appliquerErreur(champNom, 'err_nom'); return false; }
    appliquerValidation(champNom, 'err_nom'); return true;
}

function validerPrenom() {
    if (!modeleLettres.test(champPrenom.value.trim())) { appliquerErreur(champPrenom, 'err_prenom'); return false; }
    appliquerValidation(champPrenom, 'err_prenom'); return true;
}

function validerDateNaissance() {
    if (champDateNaissance.value === "") { appliquerErreur(champDateNaissance, 'err_date_naissance'); return false; }
    const dateNais = new Date(champDateNaissance.value);
    const aujourdhui = new Date();
    let age = aujourdhui.getFullYear() - dateNais.getFullYear();
    const moisDiff = aujourdhui.getMonth() - dateNais.getMonth();
    if (moisDiff < 0 || (moisDiff === 0 && aujourdhui.getDate() < dateNais.getDate())) { age--; }
    if (age < 13 || age > 120) { appliquerErreur(champDateNaissance, 'err_date_naissance'); return false; }
    appliquerValidation(champDateNaissance, 'err_date_naissance'); return true;
}

function validerAdresse() {
    if (champAdresse.value.trim().length < 6) { appliquerErreur(champAdresse, 'err_adresse'); return false; }
    appliquerValidation(champAdresse, 'err_adresse'); return true;
}

function validerEmail() {
    if (!modeleEmail.test(champEmail.value.trim())) { appliquerErreur(champEmail, 'err_email'); return false; }
    appliquerValidation(champEmail, 'err_email'); return true;
}

function validerMotDePasse1() {
    if (champMotDePasse1.value.length < 6) {
        appliquerErreur(champMotDePasse1, 'err_mot_de_passe1');
        if (champMotDePasse2.value !== "") validerMotDePasse2();
        return false;
    }
    appliquerValidation(champMotDePasse1, 'err_mot_de_passe1');
    if (champMotDePasse2.value !== "") validerMotDePasse2();
    return true;
}

function validerMotDePasse2() {
    if (champMotDePasse2.value !== champMotDePasse1.value || champMotDePasse2.value === "") {
        appliquerErreur(champMotDePasse2, 'err_mot_de_passe2');
        return false;
    }
    appliquerValidation(champMotDePasse2, 'err_mot_de_passe2');
    return true;
}

champNom.addEventListener('input', validerNom);
champPrenom.addEventListener('input', validerPrenom);
champDateNaissance.addEventListener('change', validerDateNaissance);
champAdresse.addEventListener('input', validerAdresse);
champEmail.addEventListener('input', validerEmail);
champMotDePasse1.addEventListener('input', validerMotDePasse1);
champMotDePasse2.addEventListener('input', validerMotDePasse2);

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

champNom.addEventListener('input', () => mettreAJourCompteur(champNom, 'cpt_nom'));
champPrenom.addEventListener('input', () => mettreAJourCompteur(champPrenom, 'cpt_prenom'));
champAdresse.addEventListener('input', () => mettreAJourCompteur(champAdresse, 'cpt_adresse'));
champEmail.addEventListener('input', () => mettreAJourCompteur(champEmail, 'cpt_email'));
champMotDePasse1.addEventListener('input', () => mettreAJourCompteur(champMotDePasse1, 'cpt_mot_de_passe1'));
champMotDePasse2.addEventListener('input', () => mettreAJourCompteur(champMotDePasse2, 'cpt_mot_de_passe2'));

document.getElementById('formInscription').addEventListener('submit', function(e) {
    const validationNom = validerNom();
    const validationPrenom = validerPrenom();
    const validationDate = validerDateNaissance();
    const validationAdresse = validerAdresse();
    const validationEmail = validerEmail();
    const validationMdp1 = validerMotDePasse1();
    const validationMdp2 = validerMotDePasse2();

    let validationCaptcha = true;
    if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() === "") {
        validationCaptcha = false;
        alert("Veuillez cocher la case 'Je ne suis pas un robot'.");
    }

    if (!(validationNom && validationPrenom && validationDate && validationAdresse && validationEmail && validationMdp1 && validationMdp2 && validationCaptcha)) {
        e.preventDefault(); 
    }
});
</script>

<?php include 'includes/footer.php'; ?>