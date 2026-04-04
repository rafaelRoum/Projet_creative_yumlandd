<?php
session_start();

$mon_id = $_SESSION['id'] ?? $_SESSION['utilisateur_id'] ?? null;
$mon_role = $_SESSION['role'] ?? null;

$commandes = json_decode(file_get_contents('data/commandes.json'), true) ?? [];
$utilisateurs = json_decode(file_get_contents('data/utilisateurs.json'), true) ?? [];

$mes_livraisons = array_filter($commandes, function($cmd) use ($mon_id) {
    return isset($cmd['id_livreur']) && $cmd['id_livreur'] == $mon_id;
});

function getNomClient($id_client, $liste_utilisateurs) {
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

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">Mes commandes</h2>
        
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
                <?php foreach ($mes_livraisons as $cmd): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;"><strong><?php echo $cmd['id_commande']; ?></strong></td>
                        
                        <td>
                            <strong><?php echo getNomClient($cmd['id_client'], $utilisateurs); ?></strong><br>
                        </td>
                        
                        <td>
                            <?php if($cmd['statut'] === "en livraison"): ?>
                                <select class="statut-select">
                                    <option>En livraison</option>
                                    <option>Livrée</option>
                                    <option>Abandonnée</option>
                                </select>
                            <?php elseif($cmd['statut'] === "prêt"): ?>
                                <select class="statut-select">
                                    <option>A récuperer</option>
                                    <option>récuperer</option>
                                </select>
                            <?php elseif($cmd['statut'] === "abandonnée"): ?>
                                <select class="statut-select">
                                    <option>Abandonnée</option>
                                    <option>Reprendre</option>
                                </select>
                            <?php elseif($cmd['statut'] === "terminée"): ?>
                                <p style="color:#08a021">Terminée</p>
                            <?php else: ?>
                                <p><?php echo ucfirst($cmd['statut']); ?></p>
                            <?php endif; ?>
                        </td>

                        <td style="text-align: center;"> 
                            <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Voir</a>
                            
                            <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                                <div class="modal-contenu">
                                    <a href="#" class="fermer-modal">&times;</a>
                                    <h3 style="color: #5d7358;">Commande <?php echo $cmd['id_commande']; ?></h3>
                                    <hr>
                                    <div style="text-align: left; margin: 15px 0;">
                                        <p><strong>Adresse :</strong> <?php echo htmlspecialchars($cmd['adresse']); ?></p>
                                        <p><strong>Contenu :</strong></p>
                                        <ul style="list-style: none; padding: 0;">
                                            <?php foreach ($cmd['contenu'] as $item): ?>
                                                <li style="background: #f9f9f9; margin-bottom: 5px; padding: 5px;">
                                                    • <?php echo htmlspecialchars($item['nom']); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <p><strong>Montant Total :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                    </div>
                                    <a href="#" class="btn-save-cmd" style="text-decoration: none; display: block;">Fermer</a>
                                </div>
                            </div>
                        </td>

                        <td style="text-align: center;">
                            <?php if($cmd['statut'] !== "terminée" && $cmd['statut'] !== "prête"): ?>
                                <button type="submit" class="btn-save-cmd">Enregistrer</button>
                            <?php else: ?>
                                <span style="color: #ccc;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'includes/footer.php'; ?>