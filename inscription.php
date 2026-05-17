<?php

session_start();
$message_erreur = "";
$nom = $prenom = $dateNaissance = $adresse = $email = "";

// On vérifie si le formulaire a bien été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // L'opérateur '?? ""' évite les alertes "Undefined array key" au premier chargement de la page
    $email = htmlspecialchars($_POST['email'] ?? "");
    $nom = htmlspecialchars($_POST['nom'] ?? "");
    $prenom = htmlspecialchars($_POST['prenom'] ?? "");
    $dateNaissance = htmlspecialchars($_POST['date_naissance'] ?? "");
    $adresse = htmlspecialchars($_POST['adresse'] ?? "");
    $motDePasse1 = htmlspecialchars($_POST['mot_de_passe1'] ?? "");
    $motDePasse2 = htmlspecialchars($_POST['mot_de_passe2'] ?? "");

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
            "mot_de_passe" => $motDePasse1, 
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
?>

<?php
$titre_page = "Inscriptions - Le Groin de Folie";
include 'includes/header.php';
?>

<style>
    .champ-mdp {
        position: relative;
        display: flex;
        align-items: center;
    }
    .toggle-password {
        position: absolute;
        right: 12px;
        cursor: pointer;
        user-select: none;
        width: 22px;
        height: auto;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .toggle-password:hover {
        opacity: 1;
    }
    .champ-mdp input {
        padding-right: 40px !important;
    }
    /* Classes de retour visuel en français */
    .msg-erreur-js {
        color: #ff4d4d;
        font-size: 13px;
        margin-top: 5px;
        font-weight: bold;
        display: none;
    }
    .formulaire input.saisie-erreur {
        border: 2px solid #ff4d4d;
        background-color: #fff5f5;
    }
    .formulaire input.saisie-valide {
        border: 2px solid #2ecc71;
        background-color: #f5fff7;
    }
</style>

<section class="place-cadre">
    <div class="cadre">
        <h2>Inscription</h2>
        <form method="POST" id="formInscription" novalidate>
            
            <div class="formulaire">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo $nom ?>">
                <div class="msg-erreur-js" id="err_nom">Veuillez renseigner votre nom (lettres uniquement).</div>
            </div>
            
            <div class="formulaire">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo $prenom ?>">
                <div class="msg-erreur-js" id="err_prenom">Veuillez renseigner votre prénom (lettres uniquement).</div>
            </div>
            
            <div class="formulaire">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $dateNaissance ?>">
                <div class="msg-erreur-js" id="err_date_naissance">Vous devez avoir au moins 13 ans pour vous inscrire.</div>
            </div>
            
            <div class="formulaire">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" placeholder="Adresse" value="<?php echo $adresse ?>">
                <div class="msg-erreur-js" id="err_adresse">Veuillez renseigner une adresse valide (min. 6 caractères).</div>
            </div>
            
            <div class="formulaire">
                <label for="email">Email</label>
                <!-- PHP applique automatiquement le style d'erreur rouge si l'adresse email existe déjà dans le JSON -->
                <input type="email" id="email" name="email" placeholder="Votre email" 
                       class="<?php echo !empty($message_erreur) ? 'saisie-erreur' : ''; ?>" 
                       value="<?php echo $email ?>">
                <div class="msg-erreur-js" id="err_email">Veuillez entrer une adresse email valide (ex: nom@domaine.com).</div>
                
                <?php if ($message_erreur): ?>
                    <div class="msg-erreur-js" style="display: block;"><?php echo $message_erreur; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="formulaire">
                <label for="mot_de_passe1">Mot de passe</label>
                <div class="champ-mdp">
                    <input type="password" id="mot_de_passe1" name="mot_de_passe1" placeholder="Créer un mot de passe">
                    <img src="images/oeil.png" class="toggle-password" alt="Afficher" onclick="basculerVisibiliteMdp('mot_de_passe1', this)">
                </div>
                <div class="msg-erreur-js" id="err_mot_de_passe1">Le mot de passe doit contenir au moins 6 caractères.</div>
            </div>
            
            <div class="formulaire">
                <label for="mot_de_passe2">Confirmer le mot de passe</label>
                <div class="champ-mdp">
                    <input type="password" id="mot_de_passe2" name="mot_de_passe2" placeholder="Confirmer le mot de passe">
                    <img src="images/oeil.png" class="toggle-password" alt="Afficher" onclick="basculerVisibiliteMdp('mot_de_passe2', this)">
                </div>
                <div class="msg-erreur-js" id="err_mot_de_passe2">Les deux mots de passe ne correspondent pas.</div>
            </div>
            
            <button type="submit">Créer mon compte</button>
        </form>

        <p class="lien">
            Déjà inscrit ? <a href="connexion.php">Se connecter</a>
        </p>
    </div>
</section>

<script>
// --- 1. BIENVENUE DANS LA BOÎTE À OUTILS JAVASCRIPT ---

// Gestion de la visibilité des mots de passe (Oeil / Cacher)
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

// Sélection logique des éléments du formulaire
const champNom = document.getElementById('nom');
const champPrenom = document.getElementById('prenom');
const champDateNaissance = document.getElementById('date_naissance');
const champAdresse = document.getElementById('adresse');
const champEmail = document.getElementById('email');
const champMotDePasse1 = document.getElementById('mot_de_passe1');
const champMotDePasse2 = document.getElementById('mot_de_passe2');

// Définition des modèles de vérification (Regex)
const modeleLettres = /^[a-zA-ZàâäéèêëîïôöùûüçÇÉÈÀ -]{1,50}$/;
const modeleEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Interrupteurs visuels (Rouge = erreur, Vert = valide)
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

// --- 2. LOGIQUE DE VALIDATION INDIVIDUELLE ---

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

// --- 3. ÉCOUTEURS D'ÉVÉNEMENTS (TEMPS RÉEL SUR LE CLAVIER) ---
champNom.addEventListener('input', validerNom);
champPrenom.addEventListener('input', validerPrenom);
champDateNaissance.addEventListener('change', validerDateNaissance);
champAdresse.addEventListener('input', validerAdresse);
champEmail.addEventListener('input', validerEmail);
champMotDePasse1.addEventListener('input', validerMotDePasse1);
champMotDePasse2.addEventListener('input', validerMotDePasse2);

// --- 4. LE GARDE-BARRIÈRE FINALE (SUBMIT) ---
document.getElementById('formInscription').addEventListener('submit', function(e) {
    const validationNom = validerNom();
    const validationPrenom = validerPrenom();
    const validationDate = validerDateNaissance();
    const validationAdresse = validerAdresse();
    const validationEmail = validerEmail();
    const validationMdp1 = validerMotDePasse1();
    const validationMdp2 = validerMotDePasse2();

    // Bloque l'envoi si au moins une variable contient "false"
    if (!(validationNom && validationPrenom && validationDate && validationAdresse && validationEmail && validationMdp1 && validationMdp2)) {
        e.preventDefault(); 
    }
});
</script>

</body>

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
           <h3>&nbsp;</h3>
           <a href="commande.php">Commande</a>
           <a href="livraison.php">Livraison</a>
           <a href="notation.php">Notation</a>
           <a href="administrateur.php">Admin</a>
        </div>
        <div class="footer-col">
            <h3>Contact</h3>
           <a href="#">📍 12 rue du Jambon, Paris</a>
           <a href="#">📞 01 23 45 67 89</a>
           <a href="#">✉️ contact@groindefolie.com</a>
        </div>
    </div>
</footer>

</div>
</html>