<?php
session_start();

$commandes = json_decode(file_get_contents('data/commandes.json'), true) ?? [];
$utilisateurs = json_decode(file_get_contents('data/utilisateurs.json'), true) ?? [];

$livreurs = array_filter($utilisateurs, function($u) {
    return isset($u['role']) && $u['role'] === 'livreur';
});

function getNomClient($id, $liste) {
    foreach ($liste as $u) {
        if ($u['id'] == $id) return htmlspecialchars($u['informations']['prenom'] . " " . strtoupper($u['informations']['nom']));
    }
    return "Client Inconnu";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Le Groin de Folie - Gestion des Commandes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="groin_de_folie_icons.png">
</head>

<body>
<div class="fond">

<header class="top-menu">
    <nav>
        <a href="index.php">Accueil</a>
        <a href="presentation.php">Présentation</a>
        <a href="commandes.php">Commandes</a>
        <a href="profil.php">Mon Profil</a>
    </nav>
</header>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">Tableau de Bord des Commandes</h2>
        
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th style="padding: 10px;">N°</th>
                    <th>Client</th>
                    <th>Statut</th>
                    <th>Livreur</th>
                    <th>Détails</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): ?>
                <tr>

                    <td><strong><?php echo $cmd['id_commande']; ?></strong></td>

                    <td><?php echo getNomClient($cmd['id_client'], $utilisateurs); ?></td>
                    
                    <td>
                        <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                            <?php if($cmd['statut'] === "en préparation"): ?>
                                <select class="statut-select">
                                    <option value="en préparation">En préparation</option>
                                    <option value="prête">Prête</option>
                                    <option value="en livraison">En livraison</option>
                                </select>
                            <?php endif; ?>

                            <?php if($cmd['statut'] === "prêt"): ?>
                                <select class="statut-select">
                                    <option value="prête">Prête</option>
                                    <option value="en préparation">En préparation</option>
                                    <option value="en livraison">En livraison</option>
                                </select>
                            <?php endif; ?>

                            <?php if($cmd['statut'] === "en livraison"): ?>
                                <p style="color:#ff9102">En livraison</p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($cmd['type_livraison'] === 'sur place'): ?>
                            <?php if($cmd['statut'] === "en préparation"): ?>
                                <select class="statut-select">
                                    <option value="en préparation">En préparation</option>
                                    <option value="prête">Prête</option>
                                </select>
                            <?php endif; ?>
                            <?php if ($cmd['type_livraison'] === 'prêt'): ?>
                                <p style="color:color:#ff9102">Prêt pour le client</p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($cmd['statut'] === "terminée"): ?>
                            <p style="color:#08a021">Terminée</p>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                            <?php if($cmd['statut'] === 'prêt'): ?>
                                <select class="statut-select">
                                    <option value="">Choisir livreur...</option>
                                    <?php foreach ($livreurs as $liv): ?>
                                        <option value="<?php echo $liv['id']; ?>">
                                            <?php echo ($liv['informations']['prenom']); ?>
                                        </option>
                                    <?php endforeach; ?> 
                                </select>
                            <?php endif; ?>
                            <?php if($cmd['statut'] === 'en préparation'): ?>
                                <p>En préparation</p>
                            <?php endif; ?>
                            <?php if($cmd['statut'] === 'en livraison'): ?>
                                <?php echo ($cmd['livreur']); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($cmd['type_livraison'] === 'sur place'): ?>
                            <p>Sur place</p>
                        <?php endif; ?>
                    </td>

                    <td style="text-align: center;"> 
                        <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn" >Détails</a>
                        <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                <h3>Commande <?php echo $cmd['id_commande']; ?></h3>
                                <p>Type : <?php echo $cmd['type_livraison']; ?> | Date : <?php echo $cmd['date_heure']; ?></p>
                                <hr>
                                
                                <div style="margin: 15px">
                                    <h4 style="margin-bottom: 15px;">Articles à préparer :</h4>
                                    <?php foreach ($cmd['contenu'] as $item): ?>
                                        <strong><?php echo ($item['nom']); ?></strong>
                                        <p>Note : <?php echo implode(', ', $item['options_choisies']); ?></p>
                                    <?php endforeach; ?>
                                </div>

                                <div class="paiement">
                                    <p><strong>Total :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                    <p><strong>Paiement :</strong> <?php echo strtoupper($cmd['paiement']['methode']); ?> <span style="color:#08a021"> <?php echo $cmd['paiement']['statut']; ?> </span> </p>
                                    <?php if(!empty($cmd['adresse'])): ?>
                                        <p><strong>Adresse :</strong> <?php echo ($cmd['adresse']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <button > <a href=commande.php style="color: white; text-decoration: none;">Fermer</a> </button>
                            </div>
                        </div>
                    </td>

                    <td>
                        <button type="submit">Enregistrer</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <div class="footer-fond">
        <div class="footer-col">
            <h3>Le Groin de Folie</h3>
            <p>Gestion simplifiée pour restaurateurs passionnés.</p>
        </div>
        <div class="footer-col">
            <h3>Contact</h3>
            <p>📍 12 rue du Jambon, Paris</p>
            <p>📞 01 23 45 67 89</p>
        </div>
    </div>
</footer>

</div>
</body>
</html>