<?php
session_start();

$fichier_json = 'data/utilisateurs.json';
$utilisateurs = [];

if (file_exists($fichier_json)) {
    $json_data = file_get_contents($fichier_json);
    $utilisateurs = json_decode($json_data, true) ?? [];
}

// =========================================================================
//  PARTIE 1 : TRAITEMENTS ASYNCHRONES (AJAX)
// =========================================================================
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
    header('Content-Type: application/json');

    $id_user = $input['id_utilisateur'] ?? '';
    $mise_a_jour_ok = false;

    // --- CAS A : MODIFICATION DES DROITS ---
    if ($input['action'] === 'changer_droit') {
        $nouveau_droit = $input['droit'] ?? '';
        $droits_autorises = ['normal', 'bloquer', 'desactiver'];
        
        if (in_array($nouveau_droit, $droits_autorises)) {
            foreach ($utilisateurs as $index => $user) {
                if ($user['id'] == $id_user) {
                    $utilisateurs[$index]['droit'] = $nouveau_droit;
                    $mise_a_jour_ok = true;
                    break;
                }
            }
        }
    }

    // --- CAS B : MODIFICATION DU STATUT ---
    if ($input['action'] === 'changer_statut') {
        $nouveau_statut = $input['statut'] ?? '';
        $statuts_autorises = ['Standard', 'Premium', 'VIP'];
        
        if (in_array($nouveau_statut, $statuts_autorises)) {
            foreach ($utilisateurs as $index => $user) {
                if ($user['id'] == $id_user) {
                    $utilisateurs[$index]['statut'] = $nouveau_statut;
                    $mise_a_jour_ok = true;
                    break;
                }
            }
        }
    }

    // --- CAS C : MODIFICATION DE LA REMISE ---
    if ($input['action'] === 'changer_remise') {
        $nouvelle_remise = intval($input['remise'] ?? 0);
        
        if ($nouvelle_remise >= 0 && $nouvelle_remise <= 50) {
            foreach ($utilisateurs as $index => $user) {
                if ($user['id'] == $id_user) {
                    $utilisateurs[$index]['niveau de remise'] = $nouvelle_remise;
                    $mise_a_jour_ok = true;
                    break;
                }
            }
        }
    }

    // Sauvegarde globale si tout est bon
    if ($mise_a_jour_ok) {
        file_put_contents($fichier_json, json_encode($utilisateurs, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour.']);
    }
    exit();
}
?>

<?php
$titre_page = "Administrateur - Le Groin de Folie";
include 'includes/header.php';
?>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2>Gestion des Utilisateurs</h2>            
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Remise</th>
                    <th>Droit</th>
                    <th>Détail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($user['informations']['nom'] ?? ''); ?></strong> 
                        <?php echo htmlspecialchars($user['informations']['prenom'] ?? ''); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                    </td>

                    <td>
                        <span class="role-badge role-<?php echo htmlspecialchars($user['role'] ?? ''); ?>">
                            <?php echo htmlspecialchars($user['role'] ?? ''); ?>
                        </span>
                    </td>
                    
                    <td>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <select class="select-statut" data-id="<?php echo $user['id']; ?>" style="padding: 4px; border-radius: 4px;">
                                <option value="Standard" <?php echo ($user['statut'] === 'Standard') ? 'selected' : ''; ?>>Standard</option>
                                <option value="Premium" <?php echo ($user['statut'] === 'Premium') ? 'selected' : ''; ?>>Premium</option>
                                <option value="VIP" <?php echo ($user['statut'] === 'VIP') ? 'selected' : ''; ?>>VIP</option>
                            </select>
                        </div>
                    </td>

                    <td class="admin-actions">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                            <input type="number" value="<?php echo htmlspecialchars($user['niveau de remise'] ?? '0'); ?>" class="input-remise" data-id="<?php echo $user['id']; ?>" min="0" max="50" oninput="if(this.value < 0) this.value = 0; if(this.value > 50) this.value = 50;" style="width: 50px; padding: 4px;"> 
                            <p style="margin: 0;">%</p>
                        </div>                     
                    </td>

                    <td>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <select class="select-droit" data-id="<?php echo $user['id']; ?>" style="padding: 4px; border-radius: 4px;">
                                <option value="normal" <?php echo ($user['droit'] === 'normal') ? 'selected' : ''; ?>>Normal</option>
                                <option value="bloquer" <?php echo ($user['droit'] === 'bloquer') ? 'selected' : ''; ?>>Bloquer</option>
                                <option value="desactiver" <?php echo ($user['droit'] === 'desactiver' || $user['droit'] === 'désactiver') ? 'selected' : ''; ?>>Désactiver</option>
                            </select>
                        </div>
                    </td>

                    <td>
                        <a href="#user-<?php echo $user['id']; ?>" class="voir-profil-btn">Détails</a>
                        
                        <div id="user-<?php echo $user['id']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                <h2>Fiche de <?php echo htmlspecialchars($user['informations']['prenom'] ?? ''); ?></h2>
                                <hr>
                                <div class="infos-details" style="margin-bottom: 20px; text-align: left;">
                                    <p><strong>Identifiant :</strong> <?php echo htmlspecialchars($user['id'] ?? ''); ?></p>                                    
                                    <p><strong>Nom :</strong> <?php echo strtoupper(htmlspecialchars($user['informations']['nom'] ?? '')); ?></p>                                    
                                    <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user['informations']['prenom'] ?? ''); ?></p>                     
                                    <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>                                    
                                    <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($user['informations']['naissance'] ?? ''); ?></p>                                   
                                    <p><strong>Adresse :</strong> <?php echo htmlspecialchars($user['informations']['adresse'] ?? ''); ?></p>   
                                    <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user['role'] ?? ''); ?></p>
                                    <p><strong>Statut :</strong> <span id="modal-statut-text-<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['statut'] ?? ''); ?></span></p>
                                    <p><strong>Niveau de remise :</strong> <span id="modal-remise-text-<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['niveau de remise'] ?? '0'); ?></span> %</p>
                                    <p><strong>Droit :</strong> <span id="modal-droit-text-<?php echo $user['id']; ?>" style="font-weight: bold; color: <?php echo ($user['droit'] === 'bloquer') ? 'red' : 'inherit'; ?>;"><?php echo htmlspecialchars($user['droit'] ?? ''); ?></span></p>
                                    <p><strong>Date d'inscription :</strong> <?php echo htmlspecialchars($user['dates']['inscription'] ?? ''); ?></p>
                                    <p><strong>Dernière connexion :</strong> <?php echo htmlspecialchars($user['dates']['derniere_connexion'] ?? ''); ?></p>
                                </div>
                                <div style="text-align: right;">
                                    <button type="button" class="btn-valider-modale" style="background-color: #5d7358; border: none; padding: 8px 15px; border-radius: 4px;">
                                        <a href="#!" style="color: white; text-decoration: none; font-weight: bold;">Fermer</a>
                                    </button>
                                </div> 
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Fonction globale pour envoyer les requêtes AJAX et afficher un feedback visuel (coche verte)
    function envoyerModification(DonneesPayload, idUser, elementNotifId) {
        fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(DonneesPayload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Feedback visuel temporaire (coche verte)
                const notif = document.getElementById(elementNotifId + idUser);
                if (notif) {
                    notif.style.display = "inline";
                    setTimeout(() => { notif.style.opacity = "0"; setTimeout(() => { notif.style.display = "none"; notif.style.opacity = "1"; }, 300); }, 1000);
                }
            } else {
                alert("Erreur : " + data.message);
            }
        })
        .catch(error => {
            console.error("Erreur:", error);
            alert("Erreur réseau lors de la mise à jour.");
        });
    }

    // 1. Écouteur sur le changement de DROIT
    document.querySelectorAll(".select-droit").forEach(select => {
        select.addEventListener("change", function () {
            const idUser = this.getAttribute("data-id");
            const txtDroitModal = document.getElementById("modal-droit-text-" + idUser);
            
            if (txtDroitModal) {
                txtDroitModal.textContent = this.value;
                txtDroitModal.style.color = (this.value === "bloquer") ? "red" : "inherit";
            }

            envoyerModification({
                action: "changer_droit",
                id_utilisateur: idUser,
                droit: this.value
            }, idUser, "notif-droit-");
        });
    });

    // 2. Écouteur sur le changement de STATUT
    document.querySelectorAll(".select-statut").forEach(select => {
        select.addEventListener("change", function () {
            const idUser = this.getAttribute("data-id");
            const txtStatutModal = document.getElementById("modal-statut-text-" + idUser);
            
            if (txtStatutModal) txtStatutModal.textContent = this.value;

            envoyerModification({
                action: "changer_statut",
                id_utilisateur: idUser,
                statut: this.value
            }, idUser, "notif-statut-");
        });
    });

    // 3. Écouteur sur le changement de REMISE (se déclenche dès qu'on quitte ou change le nombre)
    document.querySelectorAll(".input-remise").forEach(input => {
        input.addEventListener("change", function () {
            const idUser = this.getAttribute("data-id");
            const txtRemiseModal = document.getElementById("modal-remise-text-" + idUser);
            
            if (txtRemiseModal) txtRemiseModal.textContent = this.value;

            envoyerModification({
                action: "changer_remise",
                id_utilisateur: idUser,
                remise: this.value
            }, idUser, "notif-remise-");
        });
    });

});
</script>

<?php
include 'includes/footer.php';
?>