<?php
session_start();

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

$titre_page = "Profil - Le Groin de Folie";
include 'includes/header.php';
?>

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
            <button type="submit" name="deco" class="btn-deco">Se déconnecter</button>
        </form>
    </div>
</section>

<div id="modal-modifier-profil" class="modal-fond" style="display: none; visibility: visible; opacity: 1;">
    <div class="modal-contenu">
        <h3 id="modal-titre" style="text-align: left;">Modifier mon information</h3>
        <hr>
        <div style="text-align: left; margin: 15px 0;">
            <input type="hidden" id="input-champ-cible">
            <p><strong id="input-label">Nouvelle valeur :</strong></p>
            <input type="text" id="input-nouvelle-valeur" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-bottom: 15px;">
            <p id="msg-erreur-ajax" style="color: red; font-weight: bold; display: none;"></p>
        </div>
        <div style="text-align: right;">
            <button type="button" id="btn-annuler-profil" style="background-color: #999; color: white; border: none; padding: 8px 15px; border-radius: 4px; margin-right: 5px; cursor: pointer; font-weight: bold;">Annuler</button>
            <button type="button" id="btn-valider-profil" class="btn-valider-modale" style="color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Valider</button>
        </div>
    </div>
</div>

<div id="modal-noter-commande" class="modal-fond" style="display: none; visibility: visible; opacity: 1;">
    <div class="modal-contenu">
        <h3 style=" text-align: left;">Notation de la commande <span id="titre-modal-id-cmd"></span></h3>
        <hr>
        <input type="hidden" id="note-cmd-id-cible">
        
        <div class="formulaire-notation" id="bloc-note-livraison" style="margin: 15px 0; text-align: left;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Qualité de la livraison</label>
            <div class="etoile-conteneur" data-type="livraison" style="font-size: 25px; cursor: pointer; color: #ccc; letter-spacing: 5px;">
                <span data-value="1">★</span><span data-value="2">★</span><span data-value="3">★</span><span data-value="4">★</span><span data-value="5">★</span>
            </div>
        </div>
        
        <div class="formulaire-notation" style="margin: 15px 0; text-align: left;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Qualité des produits</label>
            <div class="etoile-conteneur" data-type="produits" style="font-size: 25px; cursor: pointer; color: #ccc; letter-spacing: 5px;">
                <span data-value="1">★</span><span data-value="2">★</span><span data-value="3">★</span><span data-value="4">★</span><span data-value="5">★</span>
            </div>
        </div>
        
        <div class="formulaire-notation" style="margin: 15px 0; text-align: left;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Commentaire (optionnel)</label>
            <textarea id="note-commentaire" placeholder="Votre avis..." style="width: 100%; height: 70px; padding: 8px; border-radius: 4px; border: 1px solid #ccc; resize: none; box-sizing: border-box;"></textarea>
        </div>
        
        <p id="msg-erreur-notation" style="color: red; font-weight: bold; display: none; text-align: left;"></p>

        <div style="text-align: right; margin-top: 15px;">
            <button type="button" id="btn-annuler-notation" style="background-color: #999; color: white; border: none; padding: 8px 15px; border-radius: 4px; margin-right: 5px; cursor: pointer; font-weight: bold;">Annuler</button>
            <button type="button" id="btn-valider-notation" class="btn-valider-modale" style="color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Envoyer ma note</button>
        </div>
    </div>
</div>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 id="commandes" class="france-ancien-livre">Historique des commandes</h2>
        
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th>N°</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Livreur/Récupérer</th>
                    <th>Détails</th>
                    <th>Noter</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><strong><?php echo $cmd['id_commande']; ?></strong></td>
                    <td><?php echo $cmd['date_heure']; ?></td>
                    <td>
                        <?php if($cmd['statut'] === "en préparation"): ?>
                            <p style="color:#2196F3">En préparation</p>
                        <?php elseif($cmd['statut'] === "prêt" || $cmd['statut'] === "prête"): ?>
                            <p style="color:#ff9102">Prêt</p>
                        <?php elseif($cmd['statut'] === "en livraison"): ?>
                            <p style="color:#ff9102">En livraison</p>
                        <?php elseif($cmd['statut'] === "terminée"): ?>
                            <p style="color:#08a021">Terminée</p>
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
                        <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Détails</a>
                        
                        <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                <h3 style="color: #5d7358; text-align: left;">Commande <?php echo $cmd['id_commande']; ?></h3>
                                <hr>
                                <div style="text-align: left; margin: 15px 0;">
                                    <p><strong> Adresse de livraison :</strong> <br><span style="color: #d4a017; font-weight: bold;"><?php echo htmlspecialchars($cmd['adresse'] ?? 'Adresse non spécifiée'); ?></span></p>
                                    <p><strong> Contenu du sac :</strong></p>
                                    <ul style="list-style: none; padding: 0;">
                                        <?php foreach ($cmd['contenu'] as $item): ?>
                                            <li style="background: #f9f9f9; margin-bottom: 5px; padding: 8px; border-left: 3px solid #5d7358;">
                                                <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p><strong> Montant :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                </div>
                                <button type="button" style="background-color: #5d7358; border: none; padding: 8px 15px; border-radius: 4px;"> 
                                    <a href="#!" style="color: white; text-decoration: none; font-weight: bold;">Fermer</a> 
                                </button>
                            </div>
                        </div>
                    </td>
                    
                    <td id="cell-note-<?php echo $cmd['id_commande']; ?>">
                        <?php if (isset($cmd['notation'])): ?>
                            <span style="color: #d4a017; font-weight: bold;">
                                Noté (★<?php echo ($cmd['type_livraison'] === 'livraison') ? round(($cmd['notation']['note_livraison'] + $cmd['notation']['note_produits'])/2, 1) : $cmd['notation']['note_produits']; ?>)
                            </span>
                        <?php else: ?>
                            <?php if ($cmd['statut'] === 'terminée'): ?>
                                <button type="button" class="voir-profil-btn btn-ouvrir-notation" 
                                        data-id="<?php echo $cmd['id_commande']; ?>" 
                                        data-type="<?php echo $cmd['type_livraison']; ?>">
                                    Noter
                                </button>
                            <?php else: ?>
                                <span style="color:#999; font-style:italic;">En attente</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>


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

    btnValider.addEventListener("click", function () {
        const champCible = inputChamp.value;
        const nouvelleValeur = inputValeur.value.trim();

        if (nouvelleValeur === "") {
            msgErreur.textContent = "La valeur ne peut pas être vide.";
            msgErreur.style.display = "block";
            return;
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

            // Si commande sur place/à emporter -> pas de note livraison requise
            if (typeCmd === "livraison") {
                blocNoteLivraison.style.display = "block";
            } else {
                blocNoteLivraison.style.display = "none";
                noteLivraisonSelectionnee = 5; // Valeur technique par défaut inoffensive
            }

            modalNotation.style.display = "flex";
        });
    });

    document.getElementById("btn-annuler-notation").addEventListener("click", function () {
        modalNotation.style.display = "none";
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

                cellAction.innerHTML = `<span style="color: #d4a017; font-weight: bold;">Noté (★${noteMoyenne})</span>`;
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