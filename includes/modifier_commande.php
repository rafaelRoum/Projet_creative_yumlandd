<?php
session_start();
header('Content-Type: application/json');

$mon_id = $_SESSION['id'] ?? null;
if (!$mon_id) {
    echo json_encode(['success' => false, 'message' => 'Non connecté.']);
    exit();
}

$donnees = json_decode(file_get_contents('php://input'), true);
$id_commande  = $donnees['id_commande'] ?? '';
$items_envoyes = $donnees['contenu'] ?? [];

$fichier_cmd   = __DIR__ . '/../data/commandes.json';
$fichier_users = __DIR__ . '/../data/utilisateurs.json';
$fichier_plats = __DIR__ . '/../data/plats.json';

$commandes    = json_decode(file_get_contents($fichier_cmd), true)   ?? [];
$utilisateurs = json_decode(file_get_contents($fichier_users), true) ?? [];
$plats        = json_decode(file_get_contents($fichier_plats), true) ?? [];

$plats_index = [];
foreach ($plats as $p) { $plats_index[$p['id']] = $p; }

$cmd_index = null;
foreach ($commandes as $i => $cmd) {
    if ($cmd['id_commande'] === $id_commande && (int)$cmd['id_client'] === (int)$mon_id && $cmd['statut'] === 'payée') {
        $cmd_index = $i;
        break;
    }
}

if ($cmd_index === null) {
    echo json_encode(['success' => false, 'message' => 'Commande introuvable ou déjà en préparation.']);
    exit();
}

$nouveau_contenu = [];
$nouveau_total   = 0.0;

foreach ($items_envoyes as $item) {
    $id_plat = (int)($item['id_plat'] ?? 0);
    $qte     = (int)($item['quantite'] ?? 0);
    if ($qte <= 0 || !isset($plats_index[$id_plat])) continue;

    $plat = $plats_index[$id_plat];
    $nouveau_contenu[] = [
        'type'           => 'plat',
        'id_item'        => $id_plat,
        'nom'            => $plat['nom'],
        'options_choisies' => ['Quantité : ' . $qte]
    ];
    $nouveau_total += $plat['prix'] * $qte;
}

if (empty($nouveau_contenu)) {
    echo json_encode(['success' => false, 'message' => 'La commande ne peut pas être vide.']);
    exit();
}

$ancien_total = (float)$commandes[$cmd_index]['paiement']['montant_total'];
$difference   = round($nouveau_total - $ancien_total, 2);

$commandes[$cmd_index]['contenu']                  = $nouveau_contenu;
$commandes[$cmd_index]['paiement']['montant_total'] = round($nouveau_total, 2);

if ($difference < 0) {
    $avoir_montant = abs($difference);
    foreach ($utilisateurs as $j => $u) {
        if ((int)$u['id'] === (int)$mon_id) {
            $utilisateurs[$j]['avoir'] = round(($utilisateurs[$j]['avoir'] ?? 0) + $avoir_montant, 2);
            break;
        }
    }
    file_put_contents($fichier_users, json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

file_put_contents($fichier_cmd, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode([
    'success'       => true,
    'nouveau_total' => round($nouveau_total, 2),
    'difference'    => $difference,
]);
