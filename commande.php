<?php
session_start();

$fichier_json_commandes = 'data/commandes.json';
$fichier_json_utilisateurs = 'data/utilisateurs.json';

$commandes = json_decode(file_get_contents($fichier_json_commandes), true) ?? [];
$utilisateurs = json_decode(file_get_contents($fichier_json_utilisateurs), true) ?? [];

$livreurs = array_filter($utilisateurs, function($u) {
    return isset($u['role']) && $u['role'] === 'livreur';
});

// ── Traitement AJAX ──────────────────────────────────────────────
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
    header('Content-Type: application/json');

    $id_commande_cible = $input['id_commande'] ?? '';
    $mise_a_jour_ok = false;

    foreach ($commandes as $index => $cmd) {
        if ($cmd['id_commande'] == $id_commande_cible) {

            if ($input['action'] === 'changer_statut') {
                $nouveau_statut = $input['statut'] ?? '';
                $commandes[$index]['statut'] = $nouveau_statut;
                if ($nouveau_statut === "en préparation" || $nouveau_statut === "prête") {
                    $commandes[$index]['livreur']    = "";
                    $commandes[$index]['id_livreur'] = "";
                }
                $mise_a_jour_ok = true;
            }

            if ($input['action'] === 'assigner_livreur') {
                $id_livreur = $input['id_livreur'] ?? '';
                if (!empty($id_livreur)) {
                    $commandes[$index]['statut']    = "en livraison";
                    $commandes[$index]['id_livreur'] = $id_livreur;
                    foreach ($livreurs as $liv) {
                        if ($liv['id'] == $id_livreur) {
                            $commandes[$index]['livreur'] = htmlspecialchars($liv['informations']['prenom'] . " " . strtoupper($liv['informations']['nom']));
                            break;
                        }
                    }
                    $mise_a_jour_ok = true;
                }
            }

            break;
        }
    }

    if ($mise_a_jour_ok) {
        file_put_contents($fichier_json_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
        $cmd_maj = $commandes[$index];
        echo json_encode([
            'success'        => true,
            'nouveau_statut' => $cmd_maj['statut'],
            'livreur_nom'    => $cmd_maj['livreur'] ?? '',
            'type_livraison' => $cmd_maj['type_livraison'],
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

function obtenirNomClient($id, $liste) {
    foreach ($liste as $u) {
        if ($u['id'] == $id) return htmlspecialchars($u['informations']['prenom'] . " " . strtoupper($u['informations']['nom']));
    }
    return "Client Inconnu";
}
?>

<?php
$titre_page = "Commande - Le Groin de Folie";
include 'includes/header.php';
?>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">Tableau de Bord des Commandes</h2>
        
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th style="padding: 10px;">N°</th>
                    <th>Client</th>
                    <th>Statut</th>
                    <th>Livreur</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): ?>
                <tr data-id="<?php echo $cmd['id_commande']; ?>" data-type="<?php echo $cmd['type_livraison']; ?>">
                    <td><strong><?php echo $cmd['id_commande']; ?></strong></td>

                    <td><?php echo obtenirNomClient($cmd['id_client'], $utilisateurs); ?></td>
                    
                    <td id="td-statut-<?php echo $cmd['id_commande']; ?>">
                        <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                            <?php if($cmd['statut'] === "en préparation" || $cmd['statut'] === "payée"): ?>
                                <select class="select-statut-cmd" data-id="<?php echo $cmd['id_commande']; ?>">
                                    <option value="en préparation" selected>En préparation</option>
                                    <option value="prête">Prête</option>
                                </select>
                            <?php elseif($cmd['statut'] === "prête"): ?>
                                <select class="select-statut-cmd" data-id="<?php echo $cmd['id_commande']; ?>">
                                    <option value="prête" selected>Prête (En attente de livreur)</option>
                                    <option value="en préparation">En préparation</option>
                                </select>
                            <?php elseif($cmd['statut'] === "en livraison"): ?>
                                <p style="color:#ff9102; font-weight: bold;">En livraison</p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($cmd['type_livraison'] === 'sur place'): ?>
                            <?php if($cmd['statut'] === "en préparation" || $cmd['statut'] === "payée"): ?>
                                <select class="select-statut-cmd" data-id="<?php echo $cmd['id_commande']; ?>">
                                    <option value="en préparation" selected>En préparation</option>
                                    <option value="prête">Prête (À servir)</option>
                                </select>
                            <?php elseif ($cmd['statut'] === 'prête'): ?>
                                <select class="select-statut-cmd" data-id="<?php echo $cmd['id_commande']; ?>">
                                    <option value="prête" selected>Prête (À servir)</option>
                                    <option value="terminée">Terminée</option>
                                </select>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if($cmd['statut'] === "terminée"): ?>
                            <p style="color:#08a021; font-weight: bold;">Terminée</p>
                        <?php endif; ?>
                    </td>

                    <td id="td-livreur-<?php echo $cmd['id_commande']; ?>">
                        <?php if ($cmd['type_livraison'] === 'livraison'): ?>
                            <?php if($cmd['statut'] === 'prête'): ?>
                                <select class="select-livreur-cmd statut-select" data-id="<?php echo $cmd['id_commande']; ?>" style="border: 2px solid #ff9102;">
                                    <option value="">Choisir livreur...</option>
                                    <?php foreach ($livreurs as $liv): ?>
                                        <option value="<?php echo $liv['id']; ?>">
                                            <?php echo htmlspecialchars($liv['informations']['prenom'] . " " . $liv['informations']['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif($cmd['statut'] === 'en préparation' || $cmd['statut'] === 'payée'): ?>
                                <p style="color: #999; font-style: italic;">Attendre la fin de préparation</p>
                            <?php elseif($cmd['statut'] === 'en livraison' || $cmd['statut'] === 'terminée'): ?>
                                <strong><?php echo htmlspecialchars($cmd['livreur'] ?? 'Non assigné'); ?></strong>
                            <?php endif; ?>
                        <?php else: ?>
                            <p style="color: #555; font-style: italic;">Sur place (Sans livreur)</p>
                        <?php endif; ?>
                    </td>

                    <td style="text-align: center;"> 
                        <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Détails</a>
                        
                        <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                <h3>Commande <?php echo $cmd['id_commande']; ?></h3>
                                <p>Type : <?php echo ucfirst($cmd['type_livraison']); ?> | Date : <?php echo $cmd['date_heure']; ?></p>
                                <hr>
                                <div style="margin: 15px; text-align: left;">
                                    <h4 style="margin-bottom: 15px;">Articles à préparer :</h4>
                                    <?php foreach ($cmd['contenu'] as $item): ?>
                                        <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                        <p style="font-size: 13px; color: #555; margin-bottom: 10px;">
                                            Note : <?php echo !empty($item['options_choisies']) ? htmlspecialchars(implode(', ', $item['options_choisies'])) : 'Aucune'; ?>
                                        </p>
                                    <?php endforeach; ?>
                                </div>
                                <div class="paiement" style="text-align: left; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                                    <p><strong>Total :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                    <p><strong>Paiement :</strong> <?php echo strtoupper($cmd['paiement']['methode']); ?> <span style="color:#08a021"> (<?php echo $cmd['paiement']['statut']; ?>) </span> </p>
                                    <?php if(!empty($cmd['adresse'])): ?>
                                        <p><strong>Adresse :</strong> <?php echo htmlspecialchars($cmd['adresse']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="#!" class="btn-lien-paiement" style="margin-top: 15px; display: inline-block;">Fermer</a> 
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
const LIVREURS = <?php
    $livreurs_js = [];
    foreach ($livreurs as $liv) {
        $livreurs_js[] = [
            'id'  => $liv['id'],
            'nom' => $liv['informations']['prenom'] . ' ' . strtoupper($liv['informations']['nom'])
        ];
    }
    echo json_encode($livreurs_js);
?>;

document.addEventListener("DOMContentLoaded", function () {

    function selectStatutLivraison(id, statut) {
        if (statut === 'prête') {
            return `<select class="select-statut-cmd statut-select" data-id="${id}">
                <option value="prête" selected>Prête (En attente de livreur)</option>
                <option value="en préparation">En préparation</option>
            </select>`;
        } else if (statut === 'en préparation' || statut === 'payée') {
            return `<select class="select-statut-cmd statut-select" data-id="${id}">
                <option value="en préparation" selected>En préparation</option>
                <option value="prête">Prête</option>
            </select>`;
        } else if (statut === 'en livraison') {
            return `<p style="color:#ff9102; font-weight:bold;">En livraison</p>`;
        } else if (statut === 'terminée') {
            return `<p style="color:#08a021; font-weight:bold;">Terminée</p>`;
        }
        return '';
    }

    function selectStatutSurPlace(id, statut) {
        if (statut === 'en préparation' || statut === 'payée') {
            return `<select class="select-statut-cmd statut-select" data-id="${id}">
                <option value="en préparation" selected>En préparation</option>
                <option value="prête">Prête (À servir)</option>
            </select>`;
        } else if (statut === 'prête') {
            return `<select class="select-statut-cmd statut-select" data-id="${id}">
                <option value="prête" selected>Prête (À servir)</option>
                <option value="terminée">Terminée</option>
            </select>`;
        } else if (statut === 'terminée') {
            return `<p style="color:#08a021; font-weight:bold;">Terminée</p>`;
        }
        return '';
    }

    function celluleLivreur(id, statut, nomLivreur) {
        if (statut === 'prête') {
            const options = LIVREURS.map(l => `<option value="${l.id}">${l.nom}</option>`).join('');
            return `<select class="select-livreur-cmd statut-select" data-id="${id}" style="border:2px solid #ff9102;">
                <option value="">Choisir livreur...</option>${options}
            </select>`;
        } else if (statut === 'en préparation' || statut === 'payée') {
            return `<p style="color:#999; font-style:italic;">Attendre la fin de préparation</p>`;
        } else if (statut === 'en livraison' || statut === 'terminée') {
            return `<strong>${nomLivreur || 'Non assigné'}</strong>`;
        }
        return '';
    }

    function mettreAJourLigne(id, nouveauStatut, nomLivreur, typeLivraison) {
        const tdStatut  = document.getElementById('td-statut-'  + id);
        const tdLivreur = document.getElementById('td-livreur-' + id);

        if (typeLivraison === 'livraison') {
            tdStatut.innerHTML  = selectStatutLivraison(id, nouveauStatut);
            tdLivreur.innerHTML = celluleLivreur(id, nouveauStatut, nomLivreur);
        } else {
            tdStatut.innerHTML  = selectStatutSurPlace(id, nouveauStatut);
            tdLivreur.innerHTML = `<p style="color:#555; font-style:italic;">Sur place (Sans livreur)</p>`;
        }

        // Réattacher les listeners sur les nouveaux éléments
        tdStatut.querySelectorAll('.select-statut-cmd').forEach(attacherStatut);
        tdLivreur.querySelectorAll('.select-livreur-cmd').forEach(attacherLivreur);
    }

    function sauvegarder(donnees, callback) {
        fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(donnees)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                callback(data);
            } else {
                alert("Erreur lors de la mise à jour.");
            }
        })
        .catch(() => alert("Erreur réseau."));
    }

    function attacherStatut(select) {
        select.addEventListener("change", function () {
            const id   = this.getAttribute("data-id");
            const type = this.closest('tr').getAttribute('data-type');
            sauvegarder(
                { action: "changer_statut", id_commande: id, statut: this.value },
                data => mettreAJourLigne(id, data.nouveau_statut, data.livreur_nom, type)
            );
        });
    }

    function attacherLivreur(select) {
        select.addEventListener("change", function () {
            if (!this.value) return;
            const id   = this.getAttribute("data-id");
            const type = this.closest('tr').getAttribute('data-type');
            sauvegarder(
                { action: "assigner_livreur", id_commande: id, id_livreur: this.value },
                data => mettreAJourLigne(id, data.nouveau_statut, data.livreur_nom, type)
            );
        });
    }

    document.querySelectorAll(".select-statut-cmd").forEach(attacherStatut);
    document.querySelectorAll(".select-livreur-cmd").forEach(attacherLivreur);
});
</script>

<?php include 'includes/footer.php'; ?>