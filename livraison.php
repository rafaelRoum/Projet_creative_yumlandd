<?php
session_start();

$mon_id = $_SESSION['id'] ?? $_SESSION['utilisateur_id'] ?? null;
$mon_nom_complet = $_SESSION['nom'] ?? null; 

$fichier_json_commandes = 'data/commandes.json';
$fichier_json_utilisateurs = 'data/utilisateurs.json';

$commandes = json_decode(file_get_contents($fichier_json_commandes), true) ?? [];
$utilisateurs = json_decode(file_get_contents($fichier_json_utilisateurs), true) ?? [];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_livreur'])) {
    $id_commande_cible = $_POST['id_commande_modif'] ?? '';
    $statut_selectionne = $_POST['nouveau_statut_livraison'] ?? '';

    $modification_effectuee = false;

    foreach ($commandes as $index => $cmd) {
        if ($cmd['id_commande'] == $id_commande_cible) {
            
            if ($statut_selectionne === "livree") {

                $commandes[$index]['statut'] = "terminée";
            } 
            elseif ($statut_selectionne === "abandonnee") {

                $commandes[$index]['statut'] = "prête";
                $commandes[$index]['livreur'] = "";
                $commandes[$index]['id_livreur'] = null;
            } 
            elseif ($statut_selectionne === "en livraison") {
                $commandes[$index]['statut'] = "en livraison";
            }

            $modification_effectuee = true;
            break;
        }
    }

    if ($modification_effectuee) {
        file_put_contents($fichier_json_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


$mes_livraisons = array_filter($commandes, function($cmd) use ($mon_id, $mon_nom_complet) {
    $correspondance_id = isset($cmd['id_livreur']) && $cmd['id_livreur'] == $mon_id;
    $correspondance_nom = isset($cmd['livreur']) && !empty($mon_nom_complet) && $cmd['livreur'] === $mon_nom_complet;
    

    return ($correspondance_id || $correspondance_nom) && $cmd['type_livraison'] === 'livraison';
});

function obtenirNomClient($id_client, $liste_utilisateurs) {
    foreach ($liste_utilisateurs as $u) {
        if ($u['id'] == $id_client) {
            return htmlspecialchars($u['informations']['prenom'] . " " . strtoupper($u['informations']['nom']));
        }
    }
    return "Client Inconnu";
}

$titre_page = "Toutes mes livraisons";
include 'includes/header.php';
?>

<main class="admin-cadre-placement" style="margin-bottom: 15%">
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">Mes commandes à livrer</h2>
        
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th style="padding: 10px;">N°</th>
                    <th>Client</th>
                    <th>Statut</th>
                    <th style="text-align: center;">Détails/Adresse</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mes_livraisons)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #777; font-style: italic;">
                            Aucune livraison ne vous est assignée pour le moment.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($mes_livraisons as $cmd): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <form method="POST" action="">
                            <input type="hidden" name="id_commande_modif" value="<?php echo $cmd['id_commande']; ?>">
                            <input type="hidden" name="action_livreur" value="1">

                            <td style="padding: 15px;"><strong><?php echo $cmd['id_commande']; ?></strong></td>
                            
                            <td>
                                <strong><?php echo obtenirNomClient($cmd['id_client'], $utilisateurs); ?></strong>
                            </td>
                            
                            <td>
                                <?php if($cmd['statut'] === "en livraison"): ?>
                                    <select name="nouveau_statut_livraison" class="statut-select">
                                        <option value="en livraison" selected>En livraison</option>
                                        <option value="livree"> Livrée (Terminer)</option>
                                        <option value="abandonnee"> Abandonner la course</option>
                                    </select>
                                <?php elseif($cmd['statut'] === "prête" || $cmd['statut'] === "prêt"): ?>
                                    <select name="nouveau_statut_livraison" class="statut-select" style="border: 2px solid #ff9102;">
                                        <option value="">À récupérer en cuisine...</option>
                                        <option value="en livraison">Prendre la commande</option>
                                    </select>
                                <?php elseif($cmd['statut'] === "terminée"): ?>
                                    <p style="color:#08a021; font-weight: bold;"> Livrée</p>
                                <?php else: ?>
                                    <p style="color: #666;"><?php echo ucfirst($cmd['statut']); ?></p>
                                <?php endif; ?>
                            </td>

                            <td style="text-align: center;"> 
                                <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Voir</a>
                                
                                <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                                    <div class="modal-contenu">
                                        <h3 style="color: #5d7358; text-align: left;">Commande <?php echo $cmd['id_commande']; ?></h3>
                                        <hr>
                                        <div style="text-align: left; margin: 15px 0;">
                                            <p><strong> Adresse de livraison :</strong> <br><span style="color: #d4a017; font-weight: bold;"><?php echo htmlspecialchars($cmd['adresse'] ?? 'Adresse non spécifiée'); ?></span></p>
                                            <p><strong> Contenu du sac :</strong></p>
                                            <ul style="list-style: none; padding: 0;">
                                                <?php foreach ($cmd['contenu'] as $item): ?>
                                                    <li style="background: #f9f9f9; margin-bottom: 5px; padding: 8px; border-left: 3px solid #5d7358;">
                                                        <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <p><strong> Montant :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                        </div>
                                        <button type="button" style="background-color: #5d7358; border: none; padding: 8px 15px; border-radius: 4px;"> 
                                            <a href="#!" style="color: white; text-decoration: none; font-weight: bold;">Fermer</a> 
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td style="text-align: center;">
                                <?php if($cmd['statut'] !== "terminée"): ?>
                                    <button type="submit" class="btn-save-cmd" style="cursor: pointer; padding: 5px 10px; font-weight: bold;">Enregistrer</button>
                                <?php else: ?>
                                    <span style="color: #08a021; font-weight: bold;">Finalisé</span>
                                <?php endif; ?>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'includes/footer.php'; ?>