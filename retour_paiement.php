<?php

require_once 'includes/fonctions.php';
require_once 'includes/getapikey.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_login();


$transaction   = $_GET['transaction'] ?? '';
$montant       = $_GET['montant']     ?? '';
$vendeur       = $_GET['vendeur']     ?? '';
$statut        = $_GET['status']      ?? '';  
$control_recu  = $_GET['control']     ?? '';


$id_commande    = $_GET['id_commande'] ?? '';
$est_supplement = isset($_GET['supplement']) && $_GET['supplement'] === '1';


$api_key      = getAPIKey($vendeur);
$control_calc = md5($api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $statut . "#");
$control_ok   = hash_equals($control_calc, $control_recu);


$fichier_commandes = 'data/commandes.json';
$commandes = file_exists($fichier_commandes)
    ? json_decode(file_get_contents($fichier_commandes), true)
    : [];

$index_cmd = null;
$cmd       = null;

foreach ($commandes as $i => $c) {
    if ($c['id_commande'] === $id_commande && $c['id_client'] == ($_SESSION['id'] ?? 0)) {
        $index_cmd = $i;
        $cmd       = $c;
        break;
    }
}

$succes  = false;
$message = '';

if (!$control_ok) {

    $message = "Erreur de sécurité : valeur de contrôle invalide.";

} elseif ($cmd === null) {

    $message = "Commande introuvable.";

} elseif ($statut === 'accepted') {

    if (!$est_supplement) {
        $commandes[$index_cmd]['statut']                         = 'payée';
        $commandes[$index_cmd]['paiement']['statut']             = 'payé';
        $commandes[$index_cmd]['paiement']['transaction_api_id'] = $transaction;
        $commandes[$index_cmd]['paiement']['date_transaction']   = date('Y-m-d H:i:s');

        file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $succes  = true;
        $message = "Paiement accepté ! Votre commande <strong>{$id_commande}</strong> est confirmée.";

    } else {
        $pending = $_SESSION['cybank_supplement'][$id_commande] ?? null;

        if ($pending) {
            $commandes[$index_cmd]['contenu']                         = $pending['nouveau_contenu'];
            $commandes[$index_cmd]['paiement']['montant_total']       = $pending['nouveau_total'];
            $commandes[$index_cmd]['paiement']['transaction_api_id']  = $transaction;
            $commandes[$index_cmd]['paiement']['date_transaction']    = date('Y-m-d H:i:s');

            file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            unset($_SESSION['cybank_supplement'][$id_commande]);

            $succes  = true;
            $message = "Supplément réglé ! La commande <strong>{$id_commande}</strong> a bien été mise à jour.";
        } else {
            $message = "Session expirée — les données du supplément sont introuvables.";
        }
    }

} elseif ($statut === 'declined') {
    if (!$est_supplement && ($cmd['statut'] ?? '') === 'en_attente_paiement') {
        array_splice($commandes, $index_cmd, 1);
        file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    $message = "Paiement refusé par CY Bank. Votre commande n'a pas été enregistrée.";

} else {
    $message = "Statut de paiement inconnu.";
}


if ($succes) {
    header("Location: profil.php#commandes");
} else {
    header("Location: panier.php?erreur=" . urlencode($message));
}
exit();