<?php
session_start();
require_once 'includes/fonctions.php';
require_login();

$fichier_json = 'data/utilisateurs.json';
$fichier_json_commandes = 'data/commandes.json';

$utilisateurs = json_decode(file_get_contents($fichier_json), true) ?? [];
$mon_id = $_SESSION['id'] ?? null; 
$mon_profil = null;

foreach ($utilisateurs as $user) {
    if ($user['id'] == $mon_id) {
        $mon_profil = $user;
        break;
    }
}

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    header('Content-Type: application/json');

    if (isset($input['action']) && $input['action'] === 'noter_commande') {
        $id_cmd = $input['id_commande'] ?? '';
        $note_livraison = intval($input['note_livraison'] ?? 0);
        $note_produits = intval($input['note_produits'] ?? 0);
        $commentaire = htmlspecialchars(trim($input['commentaire'] ?? ''));

        $toutes_les_cmd = json_decode(file_get_contents($fichier_json_commandes), true) ?? [];
        $cmd_trouvee = false;

        foreach ($toutes_les_cmd as $index => $cmd) {
            if ($cmd['id_commande'] == $id_cmd && $cmd['id_client'] == $mon_id) {
                
                if (isset($cmd['notation'])) {
                    echo json_encode(['success' => false, 'message' => 'Cette commande a déjà été notée.']);
                    exit();
                }

                $toutes_les_cmd[$index]['notation'] = [
                    'note_livraison' => $note_livraison,
                    'note_produits' => $note_produits,
                    'commentaire' => $commentaire
                ];
                $cmd_trouvee = true;
                break;
            }
        }

        if ($cmd_trouvee) {
            file_put_contents($fichier_json_commandes, json_encode($toutes_les_cmd, JSON_PRETTY_PRINT));

            $fichier_notations = 'data/notations.json';
            $notations = file_exists($fichier_notations)
                ? json_decode(file_get_contents($fichier_notations), true) ?? []
                : [];

            $cmd_notee = $toutes_les_cmd[$index];
            $notations[] = [
                'id_commande'    => $id_cmd,
                'type_livraison' => $cmd_notee['type_livraison'],
                'note_produits'  => $note_produits,
                'note_livraison' => $cmd_notee['type_livraison'] === 'livraison' ? $note_livraison : null,
                'commentaire'    => $commentaire,
                'date'           => date('Y-m-d'),
            ];

            file_put_contents($fichier_notations, json_encode($notations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Commande introuvable.']);
        }
        exit();
    }


    if (isset($input['champ'])) {
        $champ = $input['champ'];
        $valeur = trim($input['valeur'] ?? '');

        $champs_autorises = ['nom', 'prenom', 'email', 'naissance', 'adresse'];
        if (!in_array($champ, $champs_autorises) || $valeur === '') {
            echo json_encode(['success' => false, 'message' => 'Données non valides.']);
            exit();
        }

        $modification_ok = false;
        foreach ($utilisateurs as $index => $user) {
            if ($user['id'] == $mon_id) {
                if ($champ === 'email') {
                    $utilisateurs[$index]['email'] = htmlspecialchars($valeur);
                } else {
                    $utilisateurs[$index]['informations'][$champ] = htmlspecialchars($valeur);
                }
                $modification_ok = true;
                break;
            }
        }

        if ($modification_ok) {
            file_put_contents($fichier_json, json_encode($utilisateurs, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour des données.']);
        }
        exit(); 
    }
}

if (isset($_POST['deco'])) {
    verifier_token_csrf();
    session_destroy();   
    header("Location: index.php");
    exit();
}

$toutes_les_commandes = json_decode(file_get_contents($fichier_json_commandes), true) ?? [];
$commandes = [];

foreach ($toutes_les_commandes as $cmd) {
    if ($cmd['id_client'] == $mon_id) {
        $commandes[] = $cmd;
    }
}

$tous_les_plats_profil = json_decode(file_get_contents('data/plats.json'), true) ?? [];

$titre_page = "Profil - Le Groin de Folie";
include 'includes/header.php';
?>

<section class="place-cadre">
    <div class="cadre">
        <h2>Mon Profil</h2>

        <table class="tab-utilisateur">
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
                    <td id="val-nom"><?php echo strtoupper(htmlspecialchars($mon_profil['informations']['nom'] ?? '')); ?></td>
                    <td><button type="button" class="btn-modifier-profil" data-champ="nom" data-label="Nom">Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Prénom</strong></td>
                    <td id="val-prenom"><?php echo htmlspecialchars($mon_profil['informations']['prenom'] ?? ''); ?></td>
                    <td><button type="button" class="btn-modifier-profil" data-champ="prenom" data-label="Prénom">Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td id="val-email"><?php echo htmlspecialchars($mon_profil['email'] ?? ''); ?></td>
                    <td><button type="button" class="btn-modifier-profil" data-champ="email" data-label="Email">Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Naissance</strong></td>
                    <td id="val-naissance"><?php echo htmlspecialchars($mon_profil['informations']['naissance'] ?? ''); ?></td>
                    <td><button type="button" class="btn-modifier-profil" data-champ="naissance" data-label="Date de naissance">Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Adresse</strong></td>
                    <td id="val-adresse"><?php echo htmlspecialchars($mon_profil['informations']['adresse'] ?? ''); ?></td>
                    <td><button type="button" class="btn-modifier-profil" data-champ="adresse" data-label="Adresse">Modifier</button></td>
                </tr>

                <tr>
                    <td><strong>Rôle</strong></td>
                    <td><?php echo ucfirst(htmlspecialchars($mon_profil['role'] ?? '')); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Statut</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['statut'] ?? ''); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Remise</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['niveau de remise'] ?? '0'); ?> %</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Inscription</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['dates']['inscription'] ?? ''); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Dernière Connexion</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['dates']['derniere_connexion'] ?? ''); ?></td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>

        <form method="POST" style="margin-top: 30px; text-align: center;">
            <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">
            <button type="submit" name="deco" class="btn-deco">Se déconnecter</button>
        </form>
    </div>
</section>

<div id="modal-modifier-profil" class="modal-fond" style="display: none;">
    <div class="modal-contenu">
        <h3 id="modal-titre">Modifier mon information</h3>
        <hr>
        <div class="formulaire">
            <input type="hidden" id="input-champ-cible">
            <p><strong id="input-label">Nouvelle valeur :</strong></p>
            <input type="text" id="input-nouvelle-valeur" class="input-modal">
            <p id="msg-erreur-ajax" class="msg-erreur-champ"></p>
        </div>
        <div class="modal-buttons" style="align-items: stretch;">
            <button type="button" id="btn-annuler-profil" class="btn-modal btn-annuler">Annuler</button>
            <button type="button" id="btn-valider-profil" class="btn-modal btn-valider">Valider</button>
        </div>
    </div>
</div>

<div id="modal-noter-commande" class="modal-fond" style="display: none;">
    <div class="modal-contenu">
        <h3>Notation de la commande <span id="titre-modal-id-cmd"></span></h3>
        <hr>
        <input type="hidden" id="note-cmd-id-cible">
        
        <div class="formulaire-notation" id="bloc-note-livraison" style="margin: 15px 0; text-align: left;">
            <label class="formulaire" style="display:block; margin-bottom:5px;">Qualité de la livraison</label>
            <div class="etoile-conteneur" data-type="livraison" style="font-size: 25px; cursor: pointer; color: var(--c-contraste); letter-spacing: 5px;">
                <span data-value="1">★</span><span data-value="2">★</span><span data-value="3">★</span><span data-value="4">★</span><span data-value="5">★</span>
            </div>
        </div>
        
        <div class="formulaire-notation" style="margin: 15px 0; text-align: left;">
            <label class="formulaire" style="display:block; margin-bottom:5px;">Qualité des produits</label>
            <div class="etoile-conteneur" data-type="produits" style="font-size: 25px; cursor: pointer; color: var(--c-contraste); letter-spacing: 5px;">
                <span data-value="1">★</span><span data-value="2">★</span><span data-value="3">★</span><span data-value="4">★</span><span data-value="5">★</span>
            </div>
        </div>
        
        <div class="formulaire-notation" style="margin: 15px 0; text-align: left;">
            <label class="formulaire" style="display:block; margin-bottom:5px;">Commentaire (optionnel)</label>
            <textarea id="note-commentaire" placeholder="Votre avis..." class="textarea-modal"></textarea>
        </div>
        
        <p id="msg-erreur-notation" class="msg-erreur-champ" style="text-align: left;"></p>

        <div class="modal-buttons">
            <button type="button" id="btn-valider-notation" class="btn-modal btn-valider">
                Envoyer ma note
            </button>
            <button type="button" id="btn-annuler-notation" class="btn-modal btn-annuler">
                Annuler
            </button>
        </div>
    </div>
</div>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 id="commandes" class="france-ancien-livre">Historique des commandes</h2>
        
        <table class="tab-utilisateur">
            <thead>
                <tr class="entete-tableau">
                    <th>N°</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Livreur/Récupérer</th>
                    <th>Actions</th>
                    <th>Noter</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><strong><?php echo $cmd['id_commande']; ?></strong></td>
                    <td><?php echo $cmd['date_heure']; ?></td>
                    <td>
                        <?php if($cmd['statut'] === "payée"): ?>
                            <p class="statut-texte-attente">En attente</p>
                        <?php elseif($cmd['statut'] === "en préparation"): ?>
                            <p class="statut-texte-preparation">En préparation</p>
                        <?php elseif($cmd['statut'] === "prêt" || $cmd['statut'] === "prête"): ?>
                            <p class="statut-texte-pret">Prêt</p>
                        <?php elseif($cmd['statut'] === "en livraison"): ?>
                            <p class="statut-texte-livraison">En livraison</p>
                        <?php elseif($cmd['statut'] === "terminée"): ?>
                            <p class="statut-texte-termine">Terminée</p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                            <?php if($cmd['statut'] === 'prêt' || $cmd['statut'] === 'prête' || $cmd['statut'] === 'en préparation'): ?>
                                <p>Non attribué</p>
                            <?php elseif($cmd['statut'] === 'en livraison' || $cmd['statut'] === 'terminée'): ?>
                                <?php echo htmlspecialchars($cmd['livreur'] ?? 'Inconnu'); ?>
                            <?php endif; ?>
                        <?php elseif ($cmd['type_livraison'] === 'sur place'): ?>
                            <?php if($cmd['statut'] === 'prêt' || $cmd['statut'] === 'prête'): ?>
                                <p>À récupérer</p>
                            <?php else: ?>
                                <p>-</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <div style="margin-bottom: 5px;">
                            <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Détails</a>
                        </div>

                        <?php if ($cmd['statut'] === 'payée'): ?>
                        <div>
                            <a href="#" class="voir-profil-btn btn-modifier-commande" data-id="<?php echo $cmd['id_commande']; ?>" data-contenu="<?php echo htmlspecialchars(json_encode($cmd['contenu']), ENT_QUOTES); ?>" data-total="<?php echo $cmd['paiement']['montant_total']; ?>">Modifier</a>
                        </div>
                        <?php endif; ?>

                        <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                <h3 class="france-ancien-livre" style="color:var(--c-principal);">Commande <?php echo $cmd['id_commande']; ?></h3>
                                <hr>
                                <div class="formulaire">
                                    <p><strong> Adresse de livraison :</strong> <br><span class="texte-dore"><?php echo htmlspecialchars($cmd['adresse'] ?? 'Adresse non spécifiée'); ?></span></p>
                                    <p><strong> Contenu du sac :</strong></p>
                                    <ul class="liste-contenu">
                                        <?php foreach ($cmd['contenu'] as $item): ?>
                                            <li class="liste-item">
                                                <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p><strong> Montant :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                </div>
                                <button type="button" class="btn-valider-modale"> 
                                    <a href="#!" style="color: white; text-decoration: none; font-weight: bold;">Fermer</a> 
                                </button>
                            </div>
                        </div>
                    </td>
                    
                    <td id="cell-note-<?php echo $cmd['id_commande']; ?>">
                        <?php if (isset($cmd['notation'])): ?>
                            <span class="texte-dore">
                                Noté (★<?php echo ($cmd['type_livraison'] === 'livraison') ? round(($cmd['notation']['note_livraison'] + $cmd['notation']['note_produits'])/2, 1) : $cmd['notation']['note_produits']; ?>)
                            </span>
                        <?php else: ?>
                            <?php if ($cmd['statut'] === 'terminée'): ?>
<a href="#" 
   class="voir-profil-btn btn-ouvrir-notation" 
   data-id="<?php echo $cmd['id_commande']; ?>" 
   data-type="<?php echo $cmd['type_livraison']; ?>">
   Noter
</a>
                            <?php else: ?>
                                <span class="texte-info">En attente</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>


<div id="modal-modifier-commande" class="modal-fond" style="display:none;">
    <div class="modal-contenu modal-contenu-large">
        <h3>Modifier la commande <span id="mc-id-cmd"></span></h3>
        <hr>
        <?php $remise_profil = intval($mon_profil['niveau de remise'] ?? 0); ?>

        <?php if ($remise_profil > 0): ?>
            <div style="background:var(--c-fond-remise, #f0f7ee); border-left:4px solid var(--c-principal); padding:8px 12px; border-radius:4px; margin-bottom:12px;">
                <strong style="color:var(--c-principal);">Réduction <?= htmlspecialchars($mon_profil['statut'] ?? '') ?> : -<?= $remise_profil ?>% appliquée</strong>
            </div>
        <?php endif; ?>

        <table class="tab-utilisateur" style="margin-bottom:12px;">
            <thead>
                <tr class="entete-tableau" style="font-size:13px;">
                    <th style="padding:8px;">Plat</th>
                    <th style="padding:8px;">Prix unit.</th>
                    <th style="padding:8px;">Quantité</th>
                    <th style="padding:8px;">Sous-total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="mc-liste-items"></tbody>
        </table>

        <div style="border-top:1px solid var(--c-contraste); padding-top:12px; margin-bottom:12px;">
            <strong style="font-size:13px;">Ajouter un plat :</strong>
            <div style="display:flex; gap:8px; margin-top:8px; flex-wrap:wrap;">
                <select id="mc-select-plat" class="statut-select" style="flex:1; font-size:13px;">
                    <?php foreach ($tous_les_plats_profil as $p): ?>
                    <option value="<?= $p['id'] ?>" data-prix="<?= $p['prix'] ?>" data-nom="<?= htmlspecialchars($p['nom']) ?>">
                        <?= htmlspecialchars($p['nom']) ?> — <?= number_format($p['prix'], 2, ',', ' ') ?> €
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="mc-qte-ajout" value="1" min="1" max="10" class="input-qte-moyenne">
                <button type="button" id="mc-btn-ajouter" class="btn-valider-modale" style="padding:6px 14px;">+ Ajouter</button>
            </div>
        </div>

        <div style="margin:16px 0 8px 0;">
            <?php if ($remise_profil > 0): ?>
                <p id="mc-total-brut" style="color:var(--c-texte-gris); text-decoration:line-through; margin:0; font-size:13px;">0,00 €</p>
            <?php endif; ?>
            <h3 style="margin:4px 0;"><strong>Total : <span id="mc-total">0,00</span> €</strong></h3>
            <?php if ($remise_profil > 0): ?>
                <p id="mc-economie" style="color:var(--c-principal); font-size:13px; font-weight:bold; margin:2px 0 0 0;">Économie : 0,00 €</p>
            <?php endif; ?>
        </div>

        <p id="mc-msg-paiement" class="msg-paiement"></p>
        <p id="mc-msg-erreur" class="msg-erreur-champ"></p>

        <div class="modal-buttons">
            <button type="button" id="mc-btn-annuler" class="btn-modal btn-annuler">Annuler</button>
            <button type="button" id="mc-btn-valider" class="btn-modal btn-valider">Valider les modifications</button>
        </div>
    </div>
</div>

<div id="modal-paiement-supplement" class="modal-fond" style="display:none;">
    <div class="modal-contenu">
        <h2>Paiement sécurisé</h2>
        <hr>
        <div class="formulaire">
            <p>Votre commande modifiée est plus chère que l'originale.</p>
            <p>Supplément à régler : <strong id="supplement-montant" style="color:var(--c-principal); font-size:1.2em;"></strong></p>
            <p class="texte-info">Vous allez être redirigé vers notre partenaire bancaire pour finaliser le paiement.</p>
        </div>
        <button type="button" id="btn-payer-supplement" class="btn-paiement-cy">Payer avec CY Bank</button>
        <button type="button" id="btn-annuler-supplement" class="btn-deco" style="border-radius:30px; padding:14px; font-size:16px; width:100%; margin-top:10px;">Fermer</button>
    </div>
</div>

<div id="modal-remboursement" class="modal-fond" style="display:none;">
    <div class="modal-contenu" style="text-align:center;">
        <h2 style="color:var(--c-principal);">Remboursement en cours</h2>
        <hr>
        <div style="margin:20px 0;">
            <p style="font-size:1.05em;">Votre commande modifiée est moins chère que l'originale.</p>
            <p style="font-size:1.15em; margin-top:15px;">Vous serez remboursé de <strong id="remboursement-montant" style="color:var(--c-principal); font-size:1.2em;"></strong><br>sur la carte bancaire utilisée lors de l'achat.</p>
        </div>
        <button type="button" id="btn-confirmer-remboursement" class="btn-paiement-cy">OK, j'ai compris</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const boutonsModifier = document.querySelectorAll(".btn-modifier-profil");
    const modal = document.getElementById("modal-modifier-profil");
    const modalTitre = document.getElementById("modal-titre");
    const inputLabel = document.getElementById("input-label");
    const inputChamp = document.getElementById("input-champ-cible");
    const inputValeur = document.getElementById("input-nouvelle-valeur");
    const msgErreur = document.getElementById("msg-erreur-ajax");
    
    const btnAnnuler = document.getElementById("btn-annuler-profil");
    const btnValider = document.getElementById("btn-valider-profil");

    boutonsModifier.forEach(btn => {
        btn.addEventListener("click", function () {
            const champ = this.getAttribute("data-champ");
            const label = this.getAttribute("data-label");
            
            const tdValeurActuelle = document.getElementById("val-" + champ);
            let valeurActuelle = tdValeurActuelle ? tdValeurActuelle.textContent.trim() : "";

            modalTitre.textContent = "Modifier : " + label;
            inputLabel.textContent = "Nouveau " + label + " :";
            inputChamp.value = champ;
            inputValeur.value = valeurActuelle;
            msgErreur.style.display = "none";

            modal.style.display = "flex";
        });
    });

    btnAnnuler.addEventListener("click", function () {
        modal.style.display = "none";
    });

    const modeleEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const modeleLettres = /^[a-zA-ZàâäéèêëîïôöùûüçÇÉÈÀ -]{1,50}$/;

    btnValider.addEventListener("click", function () {
        const champCible = inputChamp.value;
        const nouvelleValeur = inputValeur.value.trim();

        msgErreur.style.display = "none";

        if (nouvelleValeur === "") {
            msgErreur.textContent = "La valeur ne peut pas être vide.";
            msgErreur.style.display = "block";
            return;
        }

        if (champCible === 'email' && !modeleEmail.test(nouvelleValeur)) {
            msgErreur.textContent = "Veuillez entrer une adresse email valide (ex : nom@domaine.com).";
            msgErreur.style.display = "block";
            return;
        }

        if ((champCible === 'nom' || champCible === 'prenom') && !modeleLettres.test(nouvelleValeur)) {
            msgErreur.textContent = "Ce champ ne doit contenir que des lettres.";
            msgErreur.style.display = "block";
            return;
        }

        if (champCible === 'adresse' && nouvelleValeur.length < 6) {
            msgErreur.textContent = "L'adresse doit contenir au moins 6 caractères.";
            msgErreur.style.display = "block";
            return;
        }

        if (champCible === 'naissance') {
            const dateNais = new Date(nouvelleValeur);
            if (isNaN(dateNais.getTime())) {
                msgErreur.textContent = "Veuillez entrer une date valide.";
                msgErreur.style.display = "block";
                return;
            }
            const aujourdhui = new Date();
            let age = aujourdhui.getFullYear() - dateNais.getFullYear();
            const moisDiff = aujourdhui.getMonth() - dateNais.getMonth();
            if (moisDiff < 0 || (moisDiff === 0 && aujourdhui.getDate() < dateNais.getDate())) age--;
            if (age < 13 || age > 120) {
                msgErreur.textContent = "Vous devez avoir entre 13 et 120 ans.";
                msgErreur.style.display = "block";
                return;
            }
        }

        fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                champ: champCible,
                valeur: nouvelleValeur
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const celluleCible = document.getElementById("val-" + champCible);
                if (celluleCible) {
                    celluleCible.textContent = (champCible === 'nom') ? nouvelleValeur.toUpperCase() : nouvelleValeur;
                }
                modal.style.display = "none";
            } else {
                msgErreur.textContent = data.message || "Une erreur est survenue.";
                msgErreur.style.display = "block";
            }
        })
        .catch(error => {
            console.error("Erreur Fetch:", error);
            msgErreur.textContent = "Erreur de communication avec le serveur.";
            msgErreur.style.display = "block";
        });
    });


    const modalNotation = document.getElementById("modal-noter-commande");
    const labelIdCmd = document.getElementById("titre-modal-id-cmd");
    const inputIdCmdCible = document.getElementById("note-cmd-id-cible");
    const blocNoteLivraison = document.getElementById("bloc-note-livraison");
    const txtCommentaire = document.getElementById("note-commentaire");
    const msgErreurNote = document.getElementById("msg-erreur-notation");

    let noteLivraisonSelectionnee = 0;
    let noteProduitsSelectionnee = 0;

    const conteneursEtoiles = document.querySelectorAll(".etoile-conteneur");
    conteneursEtoiles.forEach(conteneur => {
        const etoiles = conteneur.querySelectorAll("span");
        const type = conteneur.getAttribute("data-type");

        etoiles.forEach(etoile => {
            etoile.addEventListener("mouseover", function () {
                const val = parseInt(this.getAttribute("data-value"));
                colorierEtoiles(etoiles, val);
            });

            etoile.addEventListener("mouseout", function () {
                const noteEnregistree = (type === "livraison") ? noteLivraisonSelectionnee : noteProduitsSelectionnee;
                colorierEtoiles(etoiles, noteEnregistree);
            });

            etoile.addEventListener("click", function () {
                const val = parseInt(this.getAttribute("data-value"));
                if (type === "livraison") {
                    noteLivraisonSelectionnee = val;
                } else {
                    noteProduitsSelectionnee = val;
                }
                colorierEtoiles(etoiles, val);
            });
        });
    });

    function colorierEtoiles(listeEtoiles, valeur) {
        listeEtoiles.forEach(e => {
            const eVal = parseInt(e.getAttribute("data-value"));
            e.style.color = (eVal <= valeur) ? "#d4a017" : "#ccc";
        });
    }

    document.querySelectorAll(".btn-ouvrir-notation").forEach(btn => {
        btn.addEventListener("click", function () {
            const idCmd = this.getAttribute("data-id");
            const typeCmd = this.getAttribute("data-type");

            labelIdCmd.textContent = idCmd;
            inputIdCmdCible.value = idCmd;
            txtCommentaire.value = "";
            msgErreurNote.style.display = "none";
            
            noteLivraisonSelectionnee = 0;
            noteProduitsSelectionnee = 0;
            conteneursEtoiles.forEach(c => colorierEtoiles(c.querySelectorAll("span"), 0));

            if (typeCmd === "livraison") {
                blocNoteLivraison.style.display = "block";
            } else {
                blocNoteLivraison.style.display = "none";
                noteLivraisonSelectionnee = 5;
            }

            modalNotation.style.display = "flex";
        });
    });

    document.getElementById("btn-annuler-notation").addEventListener("click", function () {
        modalNotation.style.display = "none";
    });

    const modalModCmd    = document.getElementById('modal-modifier-commande');
    const mcIdCmd        = document.getElementById('mc-id-cmd');
    const mcListe        = document.getElementById('mc-liste-items');
    const mcTotal        = document.getElementById('mc-total');
    const mcMsgPaiement  = document.getElementById('mc-msg-paiement');
    const mcMsgErreur    = document.getElementById('mc-msg-erreur');
    const mcSelectPlat   = document.getElementById('mc-select-plat');
    const mcQteAjout     = document.getElementById('mc-qte-ajout');

    let mcCommandeId = '';
    let mcBtnActif   = null;
    let mcItems = [];

    const MC_REMISE = <?= intval($mon_profil['niveau de remise'] ?? 0) ?>;
    const mcTotalBrut  = document.getElementById('mc-total-brut');
    const mcEconomie   = document.getElementById('mc-economie');

    function recalculerTotal() {
        const brut   = mcItems.reduce((s, it) => s + it.prix * it.quantite, 0);
        const remise = brut * (1 - MC_REMISE / 100);
        mcTotal.textContent = remise.toFixed(2).replace('.', ',');
        if (mcTotalBrut) mcTotalBrut.textContent = brut.toFixed(2).replace('.', ',') + ' €';
        if (mcEconomie)  mcEconomie.textContent  = 'Économie : ' + (brut - remise).toFixed(2).replace('.', ',') + ' €';
        return remise;
    }

    function afficherItems() {
        mcListe.innerHTML = '';
        mcItems.forEach((it, idx) => {
            const prixRemise   = it.prix * (1 - MC_REMISE / 100);
            const sousTotal    = prixRemise * it.quantite;
            const prixAffiche  = MC_REMISE > 0
                ? `<span style="text-decoration:line-through;color:var(--c-texte-gris);font-size:11px;">${it.prix.toFixed(2).replace('.',',')} €</span><br><span style="color:var(--c-principal);font-weight:bold;">${prixRemise.toFixed(2).replace('.',',')} €</span>`
                : `${it.prix.toFixed(2).replace('.',',')} €`;

            const tr = document.createElement('tr');
            tr.style.cssText = 'border-bottom:1px solid var(--c-contraste);';
            tr.innerHTML = `
                <td style="padding:8px; font-weight:bold;">${it.nom}</td>
                <td style="padding:8px; font-size:13px;">${prixAffiche}</td>
                <td style="padding:8px;">
                    <input type="number" value="${it.quantite}" min="1" max="10"
                           class="input-qte-petite"
                           data-idx="${idx}" class="mc-qte-item">
                </td>
                <td style="padding:8px; color:var(--c-principal); font-weight:bold;" id="mc-sous-total-${idx}">${sousTotal.toFixed(2).replace('.',',')} €</td>
                <td style="padding:8px;">
                    <button type="button" data-idx="${idx}" class="mc-btn-suppr btn-save-cmd"
                            style="background:var(--c-erreur); padding:4px 8px; font-size:12px; flex-shrink:0;">✕</button>
                </td>`;
            mcListe.appendChild(tr);
        });

        mcListe.querySelectorAll('.mc-qte-item').forEach(input => {
            input.addEventListener('input', () => {
                const idx = parseInt(input.dataset.idx);
                mcItems[idx].quantite = Math.max(1, parseInt(input.value) || 1);
                const prixRemise = mcItems[idx].prix * (1 - MC_REMISE / 100);
                const st = document.getElementById('mc-sous-total-' + idx);
                if (st) st.textContent = (prixRemise * mcItems[idx].quantite).toFixed(2).replace('.', ',') + ' €';
                recalculerTotal();
            });
        });
        mcListe.querySelectorAll('.mc-btn-suppr').forEach(btn => {
            btn.addEventListener('click', () => {
                mcItems.splice(parseInt(btn.dataset.idx), 1);
                afficherItems();
                recalculerTotal();
            });
        });
        recalculerTotal();
    }

    document.querySelectorAll('.btn-modifier-commande').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            mcCommandeId = btn.dataset.id;
            mcBtnActif   = btn;
            mcIdCmd.textContent = mcCommandeId;
            mcMsgPaiement.style.display = 'none';
            mcMsgErreur.style.display = 'none';

            let contenu;
            try { contenu = JSON.parse(btn.dataset.contenu); } catch(e) { contenu = []; }

            mcItems = contenu
                .filter(it => it.type === 'plat')
                .map(it => {
                    const option = [...mcSelectPlat.options].find(o => parseInt(o.value) === parseInt(it.id_item));
                    const prix = option ? parseFloat(option.dataset.prix) : 0;
                    const match = (it.options_choisies?.[0] || '').match(/\d+/);
                    const quantite = match ? parseInt(match[0]) : 1;
                    return { id_plat: parseInt(it.id_item), nom: it.nom, prix, quantite };
                });

            afficherItems();
            modalModCmd.style.display = 'flex';
        });
    });

    document.getElementById('mc-btn-annuler').addEventListener('click', () => {
        modalModCmd.style.display = 'none';
    });

    document.getElementById('mc-btn-ajouter').addEventListener('click', () => {
        const opt    = mcSelectPlat.options[mcSelectPlat.selectedIndex];
        const idPlat = parseInt(mcSelectPlat.value);
        const nom    = opt.dataset.nom;
        const prix   = parseFloat(opt.dataset.prix);
        const qte    = Math.max(1, parseInt(mcQteAjout.value) || 1);

        const existant = mcItems.find(it => it.id_plat === idPlat);
        if (existant) {
            existant.quantite = Math.min(10, existant.quantite + qte);
        } else {
            mcItems.push({ id_plat: idPlat, nom, prix, quantite: qte });
        }
        afficherItems();
    });

    document.getElementById('mc-btn-valider').addEventListener('click', () => {
        mcMsgPaiement.style.display = 'none';
        mcMsgErreur.style.display = 'none';

        if (mcItems.length === 0) {
            mcMsgErreur.textContent = 'La commande ne peut pas être vide.';
            mcMsgErreur.style.display = 'block';
            return;
        }

        const payload = {
            id_commande: mcCommandeId,
            remise_pct: MC_REMISE,
            contenu: mcItems.map(it => ({ id_plat: it.id_plat, quantite: it.quantite }))
        };

        fetch('includes/modifier_commande.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                modalModCmd.style.display = 'none';

                const nouveauContenu = mcItems.map(it => ({
                    type: 'plat',
                    id_item: it.id_plat,
                    nom: it.nom,
                    options_choisies: ['Quantité : ' + it.quantite]
                }));

                if (mcBtnActif) {
                    mcBtnActif.dataset.contenu = JSON.stringify(nouveauContenu);
                    mcBtnActif.dataset.total   = data.nouveau_total;
                }

                const detailModal = document.getElementById('detail-' + mcCommandeId);
                if (detailModal) {
                    const ul = detailModal.querySelector('ul');
                    if (ul) {
                        ul.innerHTML = mcItems.map(it =>
                            `<li class="liste-item">
                                <strong>${it.nom}</strong> × ${it.quantite}
                            </li>`
                        ).join('');
                    }
                    detailModal.querySelectorAll('p').forEach(p => {
                        if (p.innerHTML.includes('Montant')) {
                            const totalBrut   = parseFloat(data.total_brut).toFixed(2).replace('.', ',');
                            const totalFinal  = parseFloat(data.nouveau_total).toFixed(2).replace('.', ',');
                            if (data.remise_pct > 0) {
                                p.innerHTML = `<strong> Montant :</strong> <span style="text-decoration:line-through;color:var(--c-texte-gris);font-size:0.9em;">${totalBrut} €</span> <strong style="color:var(--c-principal);">${totalFinal} €</strong> <span style="color:var(--c-principal);font-size:0.85em;">(-${data.remise_pct}%)</span>`;
                            } else {
                                p.innerHTML = `<strong> Montant :</strong> ${totalFinal} €`;
                            }
                        }
                    });
                }

                const diff = data.difference ?? 0;
                if (diff > 0) {
                    document.getElementById('supplement-montant').textContent =
                        diff.toFixed(2).replace('.', ',') + ' €';
                    document.getElementById('modal-paiement-supplement').style.display = 'flex';
                } else if (diff < 0) {
                    document.getElementById('remboursement-montant').textContent =
                        Math.abs(diff).toFixed(2).replace('.', ',') + ' €';
                    document.getElementById('modal-remboursement').style.display = 'flex';
                }
            } else {
                mcMsgErreur.textContent = data.message || 'Erreur serveur.';
                mcMsgErreur.style.display = 'block';
            }
        })
        .catch(() => {
            mcMsgErreur.textContent = 'Erreur réseau. Vérifiez que vous êtes connecté.';
            mcMsgErreur.style.display = 'block';
        });
    });

    document.getElementById('btn-payer-supplement').addEventListener('click', () => {
        document.getElementById('modal-paiement-supplement').style.display = 'none';
    });
    document.getElementById('btn-annuler-supplement').addEventListener('click', () => {
        document.getElementById('modal-paiement-supplement').style.display = 'none';
    });
    document.getElementById('btn-confirmer-remboursement').addEventListener('click', () => {
        document.getElementById('modal-remboursement').style.display = 'none';
    });

    document.getElementById("btn-valider-notation").addEventListener("click", function () {
        const idCmd = inputIdCmdCible.value;

        if (noteProduitsSelectionnee === 0 || (blocNoteLivraison.style.display === "block" && noteLivraisonSelectionnee === 0)) {
            msgErreurNote.textContent = "Veuillez donner une note en cliquant sur les étoiles.";
            msgErreurNote.style.display = "block";
            return;
        }

        fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: 'noter_commande',
                id_commande: idCmd,
                note_livraison: noteLivraisonSelectionnee,
                note_produits: noteProduitsSelectionnee,
                commentaire: txtCommentaire.value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const cellAction = document.getElementById("cell-note-" + idCmd);
                let noteMoyenne = (blocNoteLivraison.style.display === "block") 
                    ? ((noteLivraisonSelectionnee + noteProduitsSelectionnee) / 2).toFixed(1) 
                    : noteProduitsSelectionnee;

                cellAction.innerHTML = `<span class="texte-dore">Noté (★${noteMoyenne})</span>`;
                modalNotation.style.display = "none";
            } else {
                msgErreurNote.textContent = data.message;
                msgErreurNote.style.display = "block";
            }
        })
        .catch(() => {
            msgErreurNote.textContent = "Erreur réseau.";
            msgErreurNote.style.display = "block";
        });
    });
});
</script>

<?php
include 'includes/footer.php';
?>