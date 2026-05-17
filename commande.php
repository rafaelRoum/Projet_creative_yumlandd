<?php
session_start();

$fichier_json_commandes = 'data/commandes.json';
$fichier_json_utilisateurs = 'data/utilisateurs.json';

$commandes = json_decode(file_get_contents($fichier_json_commandes), true) ?? [];
$utilisateurs = json_decode(file_get_contents($fichier_json_utilisateurs), true) ?? [];

$livreurs = array_filter($utilisateurs, function($u) {
    return isset($u['role']) && $u['role'] === 'livreur';
});

// --- TRAITEMENT DE LA MISE À JOUR (CLIC ENREGISTRER) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_modifier'])) {
    $id_commande_cible = $_POST['id_commande_modif'] ?? '';
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    $id_livreur_choisi = $_POST['livreur_assigne'] ?? '';

    $modification_effectuee = false;

    foreach ($commandes as $index => $cmd) {
        if ($cmd['id_commande'] == $id_commande_cible) {
            
            // ÉTAPE 1 : Si un livreur est sélectionné, le statut passe FORCÉMENT à "en livraison"
            if (!empty($id_livreur_choisi)) {
                $commandes[$index]['statut'] = "en livraison";
                // CORRECTION : On enregistre l'ID du livreur pour le lien dynamique
                $commandes[$index]['id_livreur'] = $id_livreur_choisi; 
                
                // On cherche le nom du livreur pour l'enregistrer dans la commande
                foreach ($livreurs as $liv) {
                    if ($liv['id'] == $id_livreur_choisi) {
                        $commandes[$index]['livreur'] = htmlspecialchars($liv['informations']['prenom'] . " " . strtoupper($liv['informations']['nom']));
                        break;
                    }
                }
            } 
            // ÉTAPE 2 : Sinon, on applique le changement de statut classique du sélecteur
            else {
                if (!empty($nouveau_statut)) {
                    $commandes[$index]['statut'] = $nouveau_statut;
                    
                    // Si on rétrograde le statut en préparation ou prête, on nettoie les liaisons livreurs
                    if ($nouveau_statut === "en préparation" || $nouveau_statut === "prête") {
                        $commandes[$index]['livreur'] = "";
                        $commandes[$index]['id_livreur'] = "";
                    }
                }
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

function getNomClient($id, $liste) {
    foreach ($liste as $u) {
        if ($u['id'] == $id) return htmlspecialchars($u['informations']['prenom'] . " " . strtoupper($u['informations']['nom']));
    }
    return "Client Inconnu";
}
?>

<?php
$titre_page = "Commande - Le Groin de Folie";
include 'includes/header.php';
?>

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
                    <form method="POST" action="">
                        <input type="hidden" name="id_commande_modif" value="<?php echo $cmd['id_commande']; ?>">
                        <input type="hidden" name="action_modifier" value="1">

                        <td><strong><?php echo $cmd['id_commande']; ?></strong></td>

                        <td><?php echo getNomClient($cmd['id_client'], $utilisateurs); ?></td>
                        
                        <td>
                            <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                                <?php if($cmd['statut'] === "en préparation" || $cmd['statut'] === "payée"): ?>
                                    <select name="nouveau_statut" class="statut-select">
                                        <option value="en préparation" selected>En préparation</option>
                                        <option value="prête">Prête</option>
                                    </select>
                                <?php elseif($cmd['statut'] === "prête"): ?>
                                    <select name="nouveau_statut" class="statut-select">
                                        <option value="prête" selected>Prête (En attente de livreur)</option>
                                        <option value="en préparation">En préparation</option>
                                    </select>
                                <?php elseif($cmd['statut'] === "en livraison"): ?>
                                    <p style="color:#ff9102; font-weight: bold;">En livraison</p>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($cmd['type_livraison'] === 'sur place'): ?>
                                <?php if($cmd['statut'] === "en préparation" || $cmd['statut'] === "payée"): ?>
                                    <select name="nouveau_statut" class="statut-select">
                                        <option value="en préparation" selected>En préparation</option>
                                        <option value="prête">Prête (À servir)</option>
                                    </select>
                                <?php elseif ($cmd['statut'] === 'prête'): ?>
                                    <p style="color:#2ecc71; font-weight: bold;">Prêt pour le client</p>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if($cmd['statut'] === "terminée"): ?>
                                <p style="color:#08a021; font-weight: bold;">Terminée</p>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                                <?php if($cmd['statut'] === 'prête'): ?>
                                    <!-- Le choix du livreur s'ouvre uniquement si la commande est prête -->
                                    <select name="livreur_assigne" class="statut-select" style="border: 2px solid #ff9102;">
                                        <option value="">Choisir livreur...</option>
                                        <?php foreach ($livreurs as $liv): ?>
                                            <option value="<?php echo $liv['id']; ?>">
                                                <?php echo htmlspecialchars($liv['informations']['prenom'] . " " . $liv['informations']['nom']); ?>
                                            </option>
                                        <?php endforeach; ?> 
                                    </select>
                                <?php elseif($cmd['statut'] === 'en préparation' || $cmd['statut'] === 'payée'): ?>
                                    <p style="color: #999; font-style: italic;">Attendre la fin de préparation</p>
                                <?php elseif($cmd['statut'] === 'en livraison' || $cmd['statut'] === 'terminée'): ?>
                                    <strong> <?php echo htmlspecialchars($cmd['livreur'] ?? 'Non assigné'); ?></strong>
                                <?php endif; ?>
                            <?php else: ?>
                                <p style="color: #555; font-style: italic;">Sur place (Sans livreur)</p>
                            <?php endif; ?>
                        </td>

                        <td style="text-align: center;"> 
                            <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn">Détails</a>
                            
                            <!-- Modal -->
                            <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                                <div class="modal-contenu">
                                    <h3>Commande <?php echo $cmd['id_commande']; ?></h3>
                                    <p>Type : <?php echo ucfirst($cmd['type_livraison']); ?> | Date : <?php echo $cmd['date_heure']; ?></p>
                                    <hr>
                                    
                                    <div style="margin: 15px; text-align: left;">
                                        <h4 style="margin-bottom: 15px;">Articles à préparer :</h4>
                                        <?php foreach ($cmd['contenu'] as $item): ?>
                                            <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                            <p style="font-size: 13px; color: #555; margin-bottom: 10px;">
                                                Note : <?php echo !empty($item['options_choisies']) ? htmlspecialchars(implode(', ', $item['options_choisies'])) : 'Aucune'; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="paiement" style="text-align: left; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                                        <p><strong>Total :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                        <p><strong>Paiement :</strong> <?php echo strtoupper($cmd['paiement']['methode']); ?> <span style="color:#08a021"> (<?php echo $cmd['paiement']['statut']; ?>) </span> </p>
                                        <?php if(!empty($cmd['adresse'])): ?>
                                            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($cmd['adresse']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <a href="#!" class="btn-lien-paiement" style="margin-top: 15px; display: inline-block;">Fermer</a> 
                                </div>
                            </div>
                        </td>

                        <td>
                            <!-- Laisse le bouton Enregistrer si la commande n'est ni finie, ni sur place-prête, pour permettre la bascule complète -->
                            <?php if ($cmd['statut'] !== 'terminée'): ?>
                                <button type="submit" class="btn-save-cmd" style="cursor: pointer; padding: 5px 10px; font-weight: bold;">Enregistrer</button>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
include 'includes/footer.php';
?>