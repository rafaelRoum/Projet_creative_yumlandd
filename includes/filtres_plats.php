<?php
header('Content-Type: application/json');

$donnees = json_decode(file_get_contents('php://input'), true) ?? [];

$categories = $donnees['categories'] ?? [];
$filtres    = $donnees['filtres'] ?? [];
$tri        = $donnees['tri'] ?? '';

$plats = json_decode(file_get_contents(__DIR__ . '/../data/plats.json'), true) ?? [];

if (!empty($categories)) {
    $plats = array_filter($plats, fn($p) => in_array($p['categorie'], $categories));
}

foreach ($filtres as $filtre) {
    $plats = array_filter($plats, function ($p) use ($filtre) {
        $tags      = $p['tags'] ?? [];
        $allergenes = array_map('strtolower', $p['informations']['allergenes'] ?? []);

        switch ($filtre) {
            case 'végétarien': return in_array('végétarien', $tags);
            case 'vegan':      return in_array('vegan', $tags);
            case 'épicé':      return in_array('épicé', $tags);
            case 'salé':       return in_array('salé', $tags);
            case 'sucré':      return in_array('sucré', $tags);
            case 'sans-gluten':
                foreach ($allergenes as $a) { if (str_contains($a, 'gluten')) return false; }
                return true;
            case 'sans-lactose':
                foreach ($allergenes as $a) { if (str_contains($a, 'lait')) return false; }
                return true;
            case 'sans-oeuf':
                foreach ($allergenes as $a) { if (str_contains($a, 'œufs') || str_contains($a, 'oeufs')) return false; }
                return true;
            default: return true;
        }
    });
}

$plats = array_values($plats);

if ($tri === 'prix-asc') {
    usort($plats, fn($a, $b) => $a['prix'] <=> $b['prix']);
} elseif ($tri === 'prix-desc') {
    usort($plats, fn($a, $b) => $b['prix'] <=> $a['prix']);
} elseif ($tri === 'commandes') {
    $commandes = json_decode(file_get_contents(__DIR__ . '/../data/commandes.json'), true) ?? [];
    $compteur = [];
    foreach ($commandes as $cmd) {
        foreach ($cmd['contenu'] as $item) {
            if ($item['type'] === 'plat') {
                $compteur[$item['id_item']] = ($compteur[$item['id_item']] ?? 0) + 1;
            }
        }
    }
    usort($plats, fn($a, $b) => ($compteur[$b['id']] ?? 0) <=> ($compteur[$a['id']] ?? 0));
}

echo json_encode($plats);
