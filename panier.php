<?php
session_start();
require_once 'includes/fonctions.php';

// ── Récupération du profil client pour la remise ─────────────────
$fichier_utilisateurs = 'data/utilisateurs.json';
$utilisateurs = file_exists($fichier_utilisateurs) ? json_decode(file_get_contents($fichier_utilisateurs), true) : [];

$remise_pct   = 0;
$statut_client = 'Standard';
$utilisateur_connecte = null;

if (isset($_SESSION['id'])) {
    foreach ($utilisateurs as $u) {
        if ($u['id'] == $_SESSION['id']) {
            $utilisateur_connecte  = $u;
            $remise_pct            = intval($u['niveau de remise'] ?? 0);
            $statut_client         = $u['statut'] ?? 'Standard';
            break;
        }
    }
}

// ── AJAX : modifier quantité ──────────────────────────────────────
$inputRaw = file_get_contents('php://input');
$input    = json_decode($inputRaw, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action']) && $input['action'] === 'modifier_qte') {
    header('Content-Type: application/json');

    $id_plat     = $input['id_plat'] ?? '';
    $nouvelle_qte = intval($input['quantite'] ?? 0);

    if ($nouvelle_qte > 0) {
        $_SESSION['panier'][$id_plat] = $nouvelle_qte;
    } else {
        unset($_SESSION['panier'][$id_plat]);
    }

    // Recalculer le total avec remise
    $tous_les_plats = get_plats();
    $total_brut = 0;
    $lignes = [];

    foreach ($_SESSION['panier'] as $id => $qte) {
        foreach ($tous_les_plats as $p) {
            if ($p['id'] == $id) {
                $sous_total = $p['prix'] * $qte;
                $total_brut += $sous_total;
                $lignes[] = [
                    'id'         => $id,
                    'sous_total' => number_format($sous_total * (1 - $remise_pct / 100), 2)
                ];
                break;
            }
        }
    }

    $total_remise = $total_brut * (1 - $remise_pct / 100);

    echo json_encode([
        'success'       => true,
        'panier_vide'   => empty($_SESSION['panier']),
        'lignes'        => $lignes,
        'total_brut'    => number_format($total_brut, 2),
        'total_remise'  => number_format($total_remise, 2),
        'economie'      => number_format($total_brut - $total_remise, 2),
        'remise_pct'    => $remise_pct,
    ]);
    exit();
}

// ── Validation paiement ───────────────────────────────────────────
if (isset($_POST['valider_paiement'])) {
    $fichier_commandes = 'data/commandes.json';
    $commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];

    $tous_les_plats    = get_plats();
    $contenu_commande  = [];
    $total_brut        = 0;

    foreach ($_SESSION['panier'] as $id_plat => $qte) {
        foreach ($tous_les_plats as $p) {
            if ($p['id'] == $id_plat) {
                $contenu_commande[] = [
                    "type"           => "plat",
                    "id_item"        => $p['id'],
                    "nom"            => $p['nom'],
                    "options_choisies" => ["Quantité : " . $qte]
                ];
                $total_brut += $p['prix'] * $qte;
                break;
            }
        }
    }

    $total_final = $total_brut * (1 - $remise_pct / 100);

    $adresse_client = "";
    if ($_POST['type_livraison'] === 'livraison' && $utilisateur_connecte) {
        $adresse_client = $utilisateur_connecte['informations']['adresse'] ?? '';
    }

    $num         = count($commandes) + 1;
    $id_commande = "CMD-" . str_pad($num, 3, "0", STR_PAD_LEFT);

    $nouvelle_commande = [
        "id_commande"   => $id_commande,
        "id_client"     => $_SESSION['id'] ?? 0,
        "statut"        => "payée",
        "date_heure"    => date("Y-m-d H:i:s"),
        "type_livraison"=> $_POST['type_livraison'] === 'emporter' ? 'sur place' : 'livraison',
        "adresse"       => $adresse_client,
        "livreur"       => "",
        "id_livreur"    => "",
        "contenu"       => $contenu_commande,
        "paiement"      => [
            "statut"           => "payé",
            "methode"          => "cy bank",
            "transaction_api_id" => "CY-" . uniqid(),
            "date_transaction" => date("Y-m-d H:i:s"),
            "montant_brut"     => $total_brut,
            "remise_appliquee" => $remise_pct,
            "montant_total"    => round($total_final, 2)
        ]
    ];

    $commandes[] = $nouvelle_commande;
    file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    unset($_SESSION['panier']);
    header("Location: profil.php#commandes");
    exit();
}

// ── Affichage ─────────────────────────────────────────────────────
$titre_page     = "Mon Panier - Le Groin de Folie";
include 'includes/header.php';
$tous_les_plats = get_plats();
$total_brut     = 0;

// Précalcul du total pour la bannière remise
if (!empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $id_plat => $qte) {
        foreach ($tous_les_plats as $p) {
            if ($p['id'] == $id_plat) { $total_brut += $p['prix'] * $qte; break; }
        }
    }
}
$total_remise_affichage = $total_brut * (1 - $remise_pct / 100);
?>

<main class="admin-cadre-placement">
    <div class="admin-cadre" style="margin-bottom:20%">
        <h2 class="france-ancien-livre" style="text-align:center; margin-bottom:30px;">Votre Panier</h2>

        <?php if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])): ?>
            <p style="text-align:center;">Votre panier est vide.</p>
        <?php else: ?>

            <?php if ($remise_pct > 0): ?>
                <div style="background:#f0f7ee; border-left:4px solid #5d7358; padding:10px 15px; border-radius:4px; margin-bottom:20px;">
                    <strong style="color:#5d7358;">Réduction <?= $statut_client ?> : -<?= $remise_pct ?>% appliquée</strong>
                </div>
            <?php endif; ?>

            <table class="tab-utilisateur">
                <?php $total_brut = 0; ?>
                <thead>
                    <tr style="background-color: #f2f2f2; text-align: left;">
                        <th>Plat</th>
                        <th>Prix unit.</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id_plat => $quantite): ?>
                        <?php
                        $plat_actuel = null;
                        foreach ($tous_les_plats as $p) { if ($p['id'] == $id_plat) { $plat_actuel = $p; break; } }
                        if ($plat_actuel):
                            $sous_total_brut   = $plat_actuel['prix'] * $quantite;
                            $sous_total_remise = $sous_total_brut * (1 - $remise_pct / 100);
                            $total_brut       += $sous_total_brut;
                        ?>
                        <tr id="ligne-<?= $id_plat ?>">
                            <td><strong><?= htmlspecialchars($plat_actuel['nom']) ?></strong></td>
                            <td>
                                <?php if ($remise_pct > 0): ?>
                                    <span style="text-decoration:line-through; color:#999; font-size:12px;"><?= number_format($plat_actuel['prix'], 2) ?> €</span><br>
                                    <span style="color:#5d7358; font-weight:bold;"><?= number_format($plat_actuel['prix'] * (1 - $remise_pct / 100), 2) ?> €</span>
                                <?php else: ?>
                                    <?= number_format($plat_actuel['prix'], 2) ?> €
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="number"
                                       class="input-qte-panier"
                                       data-id="<?= $id_plat ?>"
                                       value="<?= $quantite ?>"
                                       min="0"
                                       style="width:55px; padding:4px; border:1px solid #ccc; border-radius:4px;">
                            </td>
                            <td id="sous-total-<?= $id_plat ?>">
                                <strong style="color:#5d7358;"><?= number_format($sous_total_remise, 2) ?> €</strong>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin:20px 0;">
                <?php $total_remise = $total_brut * (1 - $remise_pct / 100); ?>
                <?php if ($remise_pct > 0): ?>
                    <p id="info-original" style="color:#999; text-decoration:line-through; margin:0; font-size:14px;"><?= number_format($total_brut, 2) ?> €</p>
                <?php endif; ?>
                <h3 id="total-panier" style="margin:4px 0;">Total : <?= number_format($total_remise, 2) ?> €</h3>
                <?php if ($remise_pct > 0): ?>
                    <p id="info-economie" style="color:#5d7358; font-size:13px; font-weight:bold; margin:0;">Économie : <?= number_format($total_brut - $total_remise, 2) ?> €</p>
                <?php endif; ?>
            </div>

            <hr style="border:0; border-top:1px solid #eee; margin:40px 0;">

            <h3 class="france-ancien-livre" style="margin-bottom:25px;">Validation de la commande</h3>

            <form method="POST">
                <div class="grille-options">
                    <div class="colonne-choix">
                        <p><strong>Temps de préparation</strong></p>
                        <label class="option-label">
                            <input type="radio" name="type_preparation" value="immediat" checked>
                            Immédiate (dès que possible)
                        </label>
                        <label class="option-label">
                            <input type="radio" name="type_preparation" value="differe">
                            Différée à :
                            <input type="time" name="heure_recuperation" style="border:1px solid #ddd; border-radius:4px;">
                        </label>
                    </div>
                    <div class="colonne-choix">
                        <p><strong>Mode de retrait</strong></p>
                        <label class="option-label">
                            <input type="radio" name="type_livraison" value="emporter" checked>
                            À emporter (restaurant)
                        </label>
                        <label class="option-label">
                            <input type="radio" name="type_livraison" value="livraison">
                            En livraison (domicile)
                        </label>
                    </div>
                </div>

                <div class="conteneur-validation">
                    <a href="#modal-paiement" class="btn-lien-paiement" id="lien-payer">
                        Confirmer et payer (<span id="total-btn"><?= number_format($total_remise, 2) ?></span> €)
                    </a>
                </div>

                <div id="modal-paiement" class="modal-fond">
                    <div class="modal-contenu">
                        <h2>Paiement sécurisé</h2>
                        <hr>
                        <div class="infos-details">
                            <p>Vous allez être redirigé vers notre partenaire bancaire pour finaliser votre commande.</p>
                            <p>Total à régler : <strong id="total-modal"><?= number_format($total_remise, 2) ?></strong> €</p>
                            <?php if ($remise_pct > 0): ?>
                                <p style="color:#5d7358; font-size:13px;">Remise <?= $statut_client ?> (-<?= $remise_pct ?>%) incluse</p>
                            <?php endif; ?>
                        </div>
                        <button type="submit" name="valider_paiement">Payer avec CY Bank</button>
                        <button type="button">
                            <a href="#!" class="btn-fermer">Fermer</a>
                        </button>
                    </div>
                </div>
            </form>

        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let debounceTimer = null;

    document.querySelectorAll(".input-qte-panier").forEach(function (input) {

        function envoyerMaj(idPlat, qte) {
            fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "modifier_qte", id_plat: idPlat, quantite: qte })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                if (data.panier_vide) { location.reload(); return; }

                if (qte === 0) {
                    const ligne = document.getElementById("ligne-" + idPlat);
                    if (ligne) ligne.remove();
                }

                data.lignes.forEach(l => {
                    const td = document.getElementById("sous-total-" + l.id);
                    if (td) td.innerHTML = `<strong style="color:#5d7358;">${l.sous_total} €</strong>`;
                });

                const totalEl    = document.getElementById("total-panier");
                const totalBtn   = document.getElementById("total-btn");
                const totalModal = document.getElementById("total-modal");

                if (totalEl)    totalEl.textContent    = "Total : " + data.total_remise + " €";
                if (totalBtn)   totalBtn.textContent   = data.total_remise;
                if (totalModal) totalModal.textContent = data.total_remise;

                const infoOriginal = document.getElementById("info-original");
                const infoEconomie = document.getElementById("info-economie");
                if (infoOriginal) infoOriginal.textContent = data.total_brut + " €";
                if (infoEconomie) infoEconomie.textContent = "Économie : " + data.economie + " €";
            })
            .catch(() => console.error("Erreur réseau panier"));
        }

        input.addEventListener("input", function () {
            clearTimeout(debounceTimer);
            const idPlat = this.getAttribute("data-id");
            const qte    = parseInt(this.value) || 0;
            debounceTimer = setTimeout(() => envoyerMaj(idPlat, qte), 400);
        });

        input.addEventListener("change", function () {
            clearTimeout(debounceTimer);
            const idPlat = this.getAttribute("data-id");
            const qte    = parseInt(this.value) || 0;
            envoyerMaj(idPlat, qte);
        });
    });

});
</script>

<?php include 'includes/footer.php'; ?>