<?php
// Fonction pour récupérer tous les plats depuis le fichier JSON
function get_plats() {
    $fichier_json = 'data/plats.json';
    if (file_exists($fichier_json)) {
        $json_data = file_get_contents($fichier_json);
        return json_decode($json_data, true) ?? [];
    }
    return [];
}
?>