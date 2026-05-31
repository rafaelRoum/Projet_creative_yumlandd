<?php
session_start();
require_once 'includes/fonctions.php';
require_login();

$fichier_commandes = __DIR__ . '/../data/commandes.json';
$fichier_plats     = __DIR__ . '/../data/plats.json';

$commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];
$plats     = file_exists($fichier_plats)     ? json_decode(file_get_contents($fichier_plats),     true) : [];

header('Content-Type: application/json');

$inputRaw = file_get_contents('php://input');
$input    = json_decode($inputRaw, true);

if (!$input || !isset($input['id_commande'], $input['contenu'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit();
}

$id_commande = $input['id_commande'];
$remise_pct  = intval($input['remise_pct'] ?? 0);
$mon_id      = $_SESSION['id'] ?? $_SESSION['utilisateur_id'] ?? null;

if ($mon_id === null) {
    echo json_encode(['success' => false, 'message' => 'Non connecté - session vide.']);
    exit();
}

if (empty($commandes)) {
    echo json_encode(['success' => false, 'message' => 'Fichier commandes introuvable : ' . $fichier_commandes]);
    exit();
}

$index_cmd = null;
foreach ($commandes as $i => $cmd) {
    if ((string)$cmd['id_commande'] === (string)$id_commande && (string)$cmd['id_client'] === (string)$mon_id) {
        $index_cmd = $i;
        break;
    }
}

if ($index_cmd === null) {
    echo json_encode(['success' => false, 'message' => 'Introuvable. id_commande reçu: ' . $id_commande . ' | session id: ' . $mon_id . ' | nb commandes chargées: ' . count($commandes)]);
    exit();
}

$cmd = $commandes[$index_cmd];

// Seulement les commandes payée peuvent être modifiées
if ($cmd['statut'] !== 'payée' && $cmd['statut'] !== 'en attente') {
    echo json_encode(['success' => false, 'message' => 'Cette commande ne peut plus être modifiée.']);
    exit();
}

// Calculer le nouveau contenu
$nouveau_contenu = [];
$total_brut      = 0;

foreach ($input['contenu'] as $item) {
    $id_plat  = intval($item['id_plat']);
    $quantite = max(1, intval($item['quantite']));

    $plat_trouve = null;
    foreach ($plats as $p) {
        if ($p['id'] == $id_plat) { $plat_trouve = $p; break; }
    }

    if (!$plat_trouve) continue;

    $nouveau_contenu[] = [
        'type'            => 'plat',
        'id_item'         => $plat_trouve['id'],
        'nom'             => $plat_trouve['nom'],
        'options_choisies'=> ['Quantité : ' . $quantite]
    ];
    $total_brut += $plat_trouve['prix'] * $quantite;
}

if (empty($nouveau_contenu)) {
    echo json_encode(['success' => false, 'message' => 'La commande ne peut pas être vide.']);
    exit();
}

$total_final  = round($total_brut * (1 - $remise_pct / 100), 2);
$ancien_total = floatval($cmd['paiement']['montant_total']);
$difference   = round($total_final - $ancien_total, 2);

// Mettre à jour la commande
$commandes[$index_cmd]['contenu']                      = $nouveau_contenu;
$commandes[$index_cmd]['paiement']['montant_brut']     = $total_brut;
$commandes[$index_cmd]['paiement']['remise_appliquee'] = $remise_pct;
$commandes[$index_cmd]['paiement']['montant_total']    = $total_final;

file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode([
    'success'      => true,
    'nouveau_total'=> $total_final,
    'total_brut'   => $total_brut,
    'difference'   => $difference,
    'remise_pct'   => $remise_pct,
]);