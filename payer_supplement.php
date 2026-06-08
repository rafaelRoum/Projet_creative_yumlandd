<?php
require_once 'includes/fonctions.php';
require_once 'includes/getapikey.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_login();

$VENDEUR = 'TEST';


if (isset($_POST['confirmer_paiement'])) {

    if (!isset($_SESSION['modif_en_attente']['difference'])) {
        header('Location: profil.php');
        exit();
    }

    $modif       = $_SESSION['modif_en_attente'];
    $id_commande = $modif['id_commande'];
    $difference  = $modif['difference'];

    $api_key     = getAPIKey($VENDEUR);
    $transaction = preg_replace('/[^0-9a-zA-Z]/', '', $id_commande) . substr(uniqid(), -6);
    $montant     = number_format($difference, 2, '.', '');


    $retour = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
              . dirname($_SERVER['SCRIPT_NAME'])
              . '/retour_paiement.php?id_commande=' . urlencode($id_commande) . '&supplement=1';

    $control = md5($api_key . "#" . $transaction . "#" . $montant . "#" . $VENDEUR . "#" . $retour . "#");
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Redirection paiement…</title></head>
<body>
<p style="font-family:sans-serif;text-align:center;margin-top:60px;">Redirection vers le paiement sécurisé…</p>
<form id="form-cybank" method="POST" action="https://www.plateforme-smc.fr/cybank/index.php">
    <input type="hidden" name="transaction" value="<?= htmlspecialchars($transaction) ?>">
    <input type="hidden" name="montant"     value="<?= htmlspecialchars($montant) ?>">
    <input type="hidden" name="vendeur"     value="<?= htmlspecialchars($VENDEUR) ?>">
    <input type="hidden" name="retour"      value="<?= htmlspecialchars($retour) ?>">
    <input type="hidden" name="control"     value="<?= htmlspecialchars($control) ?>">
</form>
<script>document.getElementById('form-cybank').submit();</script>
</body>
</html>
<?php
    exit();
}


if (!isset($_POST['id_commande'], $_POST['contenu'])) {
    header('Location: profil.php');
    exit();
}

$id_commande     = trim($_POST['id_commande']);
$remise_pct      = floatval($_POST['remise_pct'] ?? 0);
$items_recus     = json_decode($_POST['contenu'], true) ?? [];
$STATUTS_BLOQUES = ['en préparation', 'prêt', 'prête', 'en livraison', 'terminée'];

$fichier_cmd = 'data/commandes.json';
$commandes   = json_decode(file_get_contents($fichier_cmd), true) ?? [];
$plats_dispo = get_plats();

$index_cmd = -1;
foreach ($commandes as $idx => $cmd) {
    if ($cmd['id_commande'] === $id_commande && (string)$cmd['id_client'] === (string)$_SESSION['id']) {
        $index_cmd = $idx;
        break;
    }
}

if ($index_cmd === -1 || in_array($commandes[$index_cmd]['statut'], $STATUTS_BLOQUES)) {
    header('Location: profil.php');
    exit();
}

$nouveau_total_brut = 0.0;
$nouveau_contenu    = [];
foreach ($items_recus as $item) {
    $id_item = intval($item['id_item']);
    $qte     = max(1, intval($item['quantite']));
    $prix    = 0.0;
    foreach ($plats_dispo as $p) {
        if (intval($p['id']) === $id_item) { $prix = floatval($p['prix']); break; }
    }
    $nouveau_total_brut += $prix * $qte;
    $nouveau_contenu[] = [
        'type'             => 'plat',
        'id_item'          => $id_item,
        'nom'              => $item['nom'],
        'options_choisies' => ['Quantité : ' . $qte],
    ];
}

$nouveau_total = round($nouveau_total_brut * (1 - $remise_pct / 100), 2);
$ancien_total  = floatval($commandes[$index_cmd]['paiement']['montant_total']);
$difference    = round($nouveau_total - $ancien_total, 2);

// Pas de supplément → appliquer directement
if ($difference <= 0.01) {
    $commandes[$index_cmd]['contenu']                     = $nouveau_contenu;
    $commandes[$index_cmd]['paiement']['montant_total']    = $nouveau_total;
    $commandes[$index_cmd]['paiement']['montant_brut']     = $nouveau_total_brut;
    $commandes[$index_cmd]['paiement']['remise_appliquee'] = $remise_pct;
    file_put_contents($fichier_cmd, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: profil.php');
    exit();
}

// Supplément → stocker en session avec la clé attendue par retour_paiement.php
$_SESSION['cybank_supplement'][$id_commande] = [
    'nouveau_contenu' => $nouveau_contenu,
    'nouveau_total'   => $nouveau_total,
    'remise_pct'      => $remise_pct,
];

// Garder aussi difference pour l'affichage étape 2
$_SESSION['modif_en_attente'] = [
    'id_commande' => $id_commande,
    'difference'  => $difference,
];

// Infos client pour l'affichage
$utilisateurs  = json_decode(file_get_contents('data/utilisateurs.json'), true) ?? [];
$statut_client = 'Standard';
foreach ($utilisateurs as $u) {
    if ($u['id'] == $_SESSION['id']) { $statut_client = $u['statut'] ?? 'Standard'; break; }
}

include 'includes/header.php';
?>

<section class="place-cadre">
    <div class="cadre">
        <h2>Paiement sécurisé</h2>
        <hr>
        <div class="formulaire">
            <p>Vous allez être redirigé vers notre partenaire bancaire <strong>CY Bank</strong> pour finaliser la modification de votre commande.</p>
            <p style="margin-top:16px;">Supplément à régler :
                <strong style="color:var(--c-principal);font-size:1.3em;"><?= number_format($difference, 2, ',', '') ?> €</strong>
            </p>
            <?php if ($remise_pct > 0): ?>
                <p class="texte-info" style="margin-top:4px;">Remise <?= htmlspecialchars($statut_client) ?> (-<?= $remise_pct ?>%) déjà incluse</p>
            <?php endif; ?>
            <p class="texte-info" style="margin-top:8px;">Commande : <strong><?= htmlspecialchars($id_commande) ?></strong></p>
        </div>
        <form method="POST" action="payer_supplement.php">
            <button type="submit" name="confirmer_paiement">Payer avec CY Bank</button>
        </form>
        <div style="margin-top:10px;">
            <a href="profil.php" class="btn-lien-paiement" style="background:var(--c-annuler);">Annuler</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>