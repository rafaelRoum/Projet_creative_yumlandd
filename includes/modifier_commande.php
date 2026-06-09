<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../includes/getapikey.php';

header('Content-Type: application/json');

$VENDEUR = 'TEST'; 

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['id_commande']) || empty($input['contenu'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit();
}

$id_commande     = trim($input['id_commande']);
$items_recus     = $input['contenu'];
$remise_pct      = floatval($input['remise_pct'] ?? 0);
$STATUTS_BLOQUES = ['en préparation', 'prêt', 'prête', 'en livraison', 'terminée'];

$fichier_cmd = __DIR__ . '/../data/commandes.json';
$commandes   = json_decode(file_get_contents($fichier_cmd), true) ?? [];
$plats_dispo = get_plats();


$index_cmd = -1;
foreach ($commandes as $idx => $cmd) {
    if ($cmd['id_commande'] === $id_commande && (string)$cmd['id_client'] === (string)$_SESSION['id']) {
        $index_cmd = $idx;
        break;
    }
}
if ($index_cmd === -1) {
    echo json_encode(['success' => false, 'message' => 'Commande introuvable.']);
    exit();
}
if (in_array($commandes[$index_cmd]['statut'], $STATUTS_BLOQUES)) {
    echo json_encode(['success' => false, 'message' => 'Cette commande ne peut plus être modifiée.']);
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


if ($difference > 0.01) {


    $_SESSION['modif_en_attente'] = [
        'id_commande'     => $id_commande,
        'nouveau_contenu' => $nouveau_contenu,
        'nouveau_total'   => $nouveau_total,
        'remise_pct'      => $remise_pct,
    ];

    $api_key     = getAPIKey($VENDEUR);
    $transaction = preg_replace('/[^0-9a-zA-Z]/', '', $id_commande) . substr(uniqid(), -6);
    $montant_str = number_format($difference, 2, '.', '');
    $retour      = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
                   . dirname($_SERVER['SCRIPT_NAME']) . '/retour_paiement.php';
    $control     = md5($api_key . '#' . $transaction . '#' . $montant_str . '#' . $VENDEUR . '#' . $retour . '#');

    echo json_encode([
        'success'    => true,
        'action'     => 'supplement',
        'difference' => $difference,
        'cybank'     => [
            'action'      => 'https://www.plateforme-smc.fr/cybank/index.php',
            'transaction' => $transaction,
            'montant'     => $montant_str,
            'vendeur'     => $VENDEUR,
            'retour'      => $retour,
            'control'     => $control,
        ],
    ]);
    exit();
}


$commandes[$index_cmd]['contenu']                     = $nouveau_contenu;
$commandes[$index_cmd]['paiement']['montant_total']    = $nouveau_total;
$commandes[$index_cmd]['paiement']['montant_brut']     = $nouveau_total_brut;
$commandes[$index_cmd]['paiement']['remise_appliquee'] = $remise_pct;
file_put_contents($fichier_cmd, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true, 'action' => 'applique']);