<?php
session_start();
require_once 'includes/fonctions.php';

$titre_page = "Mon Panier - Le Groin de Folie";
include 'includes/header.php';

// On récupère les plats pour faire la correspondance
$tous_les_plats = get_plats();
$total_commande = 0;
?>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 class="france-ancien-livre">Votre Panier</h2>

        <?php if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])): ?>
            <p class="panier-vide-msg">Votre panier est actuellement vide.</p>
            <div class="panier-vide-actions">
                <button class="btn-inline">
                    <a href="presentation.php" class="lien-bouton">Retour à la carte</a>
                </button>
            </div>
            
        <?php else: ?>
            <table class="tab-utilisateur tab-panier">
                <thead>
                    <tr>
                        <th>Plat</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id_plat => $quantite): ?>
                        <?php
                        $plat_actuel = null;
                        foreach ($tous_les_plats as $p) {
                            if ($p['id'] == $id_plat) {
                                $plat_actuel = $p;
                                break;
                            }
                        }

                        if ($plat_actuel):
                            $sous_total = $plat_actuel['prix'] * $quantite;
                            $total_commande += $sous_total;
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($plat_actuel['nom']); ?></strong></td>
                            <td><?php echo number_format($plat_actuel['prix'], 2, ',', ' '); ?> €</td>
                            <td><?php echo $quantite; ?></td>
                            <td><strong class="texte-orange"><?php echo number_format($sous_total, 2, ',', ' '); ?> €</strong></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 class="panier-total">
                Total à payer : <?php echo number_format($total_commande, 2, ',', ' '); ?> €
            </h3>

            <hr class="panier-separation">

            <h3 class="france-ancien-livre titre-validation">Validation de la commande</h3>
            <form action="valider_commande.php" method="POST">
                
                <div class="form-groupe-panier">
                    <p class="label-gras">Préparation :</p>
                    <input type="radio" id="immediat" name="type_preparation" value="immediat" checked>
                    <label for="immediat">Immédiate (dès que possible)</label><br>
                    
                    <input type="radio" id="differe" name="type_preparation" value="differe">
                    <label for="differe">Différée (choisir une heure)</label>
                    <input type="time" name="heure_recuperation" class="input-time-panier">
                </div>

                <div class="form-groupe-panier">
                    <p class="label-gras">Mode de retrait :</p>
                    <input type="radio" id="emporter" name="type_livraison" value="emporter" checked>
                    <label for="emporter">À emporter</label><br>
                    
                    <input type="radio" id="livraison" name="type_livraison" value="livraison">
                    <label for="livraison">En livraison</label>
                </div>

                <div class="panier-actions-droite">
                    <button type="submit" class="btn-valider">Procéder au paiement</button>
                </div>
            </form>



        <?php endif; ?>



    </div>
    </div>
    

<?php
include 'includes/footer.php';
?>