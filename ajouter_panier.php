<?php
session_start();
require_once 'includes/fonctions.php';
require_login();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_plat'], $_POST['quantite'])) {
    
    $id_plat = (int)$_POST['id_plat'];
    $quantite = (int)$_POST['quantite'];


    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }


    if (isset($_SESSION['panier'][$id_plat])) {
        $_SESSION['panier'][$id_plat] += $quantite;
    } else {
        $_SESSION['panier'][$id_plat] = $quantite;
    }
}


header('Location: presentation.php');
exit;