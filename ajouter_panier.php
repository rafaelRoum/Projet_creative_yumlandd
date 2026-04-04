<?php
session_start();

// On vérifie que le formulaire a bien envoyé un ID et une quantité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_plat'], $_POST['quantite'])) {
    
    $id_plat = (int)$_POST['id_plat'];
    $quantite = (int)$_POST['quantite'];

    // Si le panier n'existe pas encore dans la session, on le crée
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // On ajoute au panier ou on augmente la quantité si le plat y est déjà
    if (isset($_SESSION['panier'][$id_plat])) {
        $_SESSION['panier'][$id_plat] += $quantite;
    } else {
        $_SESSION['panier'][$id_plat] = $quantite;
    }
}

// On renvoie l'utilisateur vers la page de présentation
header('Location: presentation.php');
exit;