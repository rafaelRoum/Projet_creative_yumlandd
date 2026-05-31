<?php
session_start();

$mon_id = $_SESSION['id'] ?? $_SESSION['utilisateur_id'] ?? null;
$mon_nom_complet = $_SESSION['nom'] ?? null; 

$fichier_json_commandes = 'data/commandes.json';
$fichier_json_utilisateurs = 'data/utilisateurs.json';

$commandes = json_decode(file_get_contents($fichier_json_commandes), true) ?? [];
$utilisateurs = json_decode(file_get_contents($fichier_json_utilisateurs), true) ?? [];

// ── Traitement AJAX ──────────────────────────────────────────────
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
    header('Content-Type: application/json');

    $id_commande_cible = $input['id_commande'] ?? '';
    $statut_selectionne = $input['statut'] ?? '';
    $mise_a_jour_ok = false;

    foreach ($commandes as $index => $cmd) {
        if ($cmd['id_commande'] == $id_commande_cible) {
            if ($statut_selectionne === "livree") {
                $commandes[$index]['statut'] = "terminée";
            } elseif ($statut_selectionne === "abandonnee") {
                $commandes[$index]['statut']    = "prête";
                $commandes[$index]['livreur']   = "";
                $commandes[$index]['id_livreur'] = null;
            } elseif ($statut_selectionne === "en livraison") {
                $commandes[$index]['statut'] = "en livraison";
            }
            $mise_a_jour_ok = true;
            break;
        }
    }

    if ($mise_a_jour_ok) {
        file_put_contents($fichier_json_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'nouveau_statut' => $commandes[$index]['statut']]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

$mes_livraisons = array_filter($commandes, function($cmd) use ($mon_id, $mon_nom_complet) {
    $correspondance_id  = isset($cmd['id_livreur']) && $cmd['id_livreur'] == $mon_id;
    $correspondance_nom = isset($cmd['livreur']) && !empty($mon_nom_complet) && $cmd['livreur'] === $mon_nom_complet;
    return ($correspondance_id || $correspondance_nom) && $cmd['type_livraison'] === 'livraison';
});

function obtenirNomClient($id_client, $liste_utilisateurs) {
    foreach ($liste_utilisateurs as $u) {
        if ($u['id'] == $id_client)
            return htmlspecialchars($u['informations']['prenom'] . " " . strtoupper($u['informations']['nom']));
    }
    return "Client Inconnu";
}

$titre_page = "Toutes mes livraisons";
include 'includes/header.php';
?>

<main class="admin-cadre-placement" style="margin-bottom: 15%">
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">Mes commandes à livrer</h2>
        
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th style="padding: 10px;">N°</th>
                    <th>Client</th>
                    <th>Statut</th>
                    <th style="text-align: center;">Détails/Adresse</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mes_livraisons)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #777; font-style: italic;">
                            Aucune livraison ne vous est assignée pour le moment.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($mes_livraisons as $cmd): ?>
                    <tr style="border-bottom: 1px solid #eee;" data-id="<?php echo $cmd['id_commande']; ?>">
                        <td style="padding: 15px;"><strong><?php echo $cmd['id_commande']; ?></strong></td>
                        
                        <td><strong><?php echo obtenirNomClient($cmd['id_client'], $utilisateurs); ?></strong></td>
                        
                        <td id="td-statut-<?php echo $cmd['id_commande']; ?>">
                            <?php if($cmd['statut'] === "en livraison"): ?>
                                <select class="select-statut-liv" data-id="<?php echo $cmd['id_commande']; ?>" class="statut-select">
                                    <option value="en livraison" selected>En livraison</option>
                                    <option value="livree">Livrée (Terminer)</option>
                                    <option value="abandonnee">Abandonner la course</option>
                                </select>
                            <?php elseif($cmd['statut'] === "prête" || $cmd['statut'] === "prêt"): ?>
                                <select class="select-statut-liv" data-id="<?php echo $cmd['id_commande']; ?>" class="statut-select" style="border: 2px solid #ff9102;">
                                    <option value="">À récupérer en cuisine...</option>
                                    <option value="en livraison">Prendre la commande</option>
                                </select>
                            <?php elseif($cmd['statut'] === "terminée"): ?>
                                <p style="color:#08a021; font-weight: bold;">Livrée</p>
                            <?php else: ?>
                                <p style="color: #666;"><?php echo ucfirst($cmd['statut']); ?></p>
                            <?php endif; ?>
                        </td>

                        <td style="text-align: center;"> 
                            <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Voir</a>
                            
                            <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                                <div class="modal-contenu">
                                    <h3 style="color: #5d7358; text-align: left;">Commande <?php echo $cmd['id_commande']; ?></h3>
                                    <hr>
                                    <div style="text-align: left; margin: 15px 0;">
                                        <p><strong>Adresse de livraison :</strong> <br><span style="color: #d4a017; font-weight: bold;"><?php echo htmlspecialchars($cmd['adresse'] ?? 'Adresse non spécifiée'); ?></span></p>
                                        <p><strong>Contenu du sac :</strong></p>
                                        <ul style="list-style: none; padding: 0;">
                                            <?php foreach ($cmd['contenu'] as $item): ?>
                                                <li style="background: #f9f9f9; margin-bottom: 5px; padding: 8px; border-left: 3px solid #5d7358;">
                                                    <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <p><strong>Montant :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
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
document.addEventListener("DOMContentLoaded", function () {

    function celluleStatut(id, statut) {
        if (statut === 'en livraison') {
            return `<select class="select-statut-liv statut-select" data-id="${id}">
                <option value="en livraison" selected>En livraison</option>
                <option value="livree">Livrée (Terminer)</option>
                <option value="abandonnee">Abandonner la course</option>
            </select>`;
        } else if (statut === 'prête' || statut === 'prêt') {
            return `<select class="select-statut-liv statut-select" data-id="${id}" style="border:2px solid #ff9102;">
                <option value="">À récupérer en cuisine...</option>
                <option value="en livraison">Prendre la commande</option>
            </select>`;
        } else if (statut === 'terminée') {
            return `<p style="color:#08a021; font-weight:bold;">Livrée</p>`;
        }
        return `<p style="color:#666;">${statut}</p>`;
    }

    function attacherSelect(select) {
        select.addEventListener("change", function () {
            if (!this.value) return;
            const id = this.getAttribute("data-id");
            fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "changer_statut", id_commande: id, statut: this.value })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const td = document.getElementById('td-statut-' + id);
                    td.innerHTML = celluleStatut(id, data.nouveau_statut);
                    td.querySelectorAll('.select-statut-liv').forEach(attacherSelect);
                } else {
                    alert("Erreur lors de la mise à jour.");
                }
            })
            .catch(() => alert("Erreur réseau."));
        });
    }

    document.querySelectorAll(".select-statut-liv").forEach(attacherSelect);
});
</script>

<?php include 'includes/footer.php'; ?>