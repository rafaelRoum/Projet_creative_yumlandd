<?php
function get_plats() {
    $fichier_json = 'data/plats.json';
    if (file_exists($fichier_json)) {
        $json_data = file_get_contents($fichier_json);
        return json_decode($json_data, true) ?? [];
    }
    return [];
}

function require_login() {
    if (!isset($_SESSION['id'])) {
        header('Location: connexion.php');
        exit();
    }
}

function require_role(string $role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        header('Location: index.php');
        exit();
    }
}

function generer_token_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifier_token_csrf() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die('Requête invalide.');
    }
}

?>