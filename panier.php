<?php
session_start();
require_once 'includes/fonctions.php';

if (isset($_POST['modifier_qte'])) {
    $id_plat = $_POST['id_plat'];
    $nouvelle_qte = (int)$_POST['quantite'];
    if ($nouvelle_qte > 0) {
        $_SESSION['panier'][$id_plat] = $nouvelle_qte;
    } else {
        unset($_SESSION['panier'][$id_plat]);
    }
    header("Location: panier.php");
    exit();
}

$titre_page = "Mon Panier - Le Groin de Folie";
include 'includes/header.php';
$tous_les_plats = get_plats();
$total_commande = 0;
?>

<main class="admin-cadre-placement">
    <div class="admin-cadre" style="margin-bottom:20%">
        <h2 class="france-ancien-livre" style="text-align:center; margin-bottom:30px;">Votre Panier</h2>

        <?php if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])): ?>
            <p style="text-align:center;">Votre panier est vide.</p>
        <?php else: ?>

            <table class="tab-utilisateur">
                <thead>
                    <tr style="background-color: #f2f2f2; text-align: left;">
                        <th>Plat</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Modifier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id_plat => $quantite): ?>
                        <?php
                        foreach ($tous_les_plats as $p) { if ($p['id'] == $id_plat) { $plat_actuel = $p; break; } }
                        if ($plat_actuel):
                            $sous_total = $plat_actuel['prix'] * $quantite;
                            $total_commande += $sous_total;
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($plat_actuel['nom']) ?></strong></td>
                            <td><?= number_format($plat_actuel['prix'], 2) ?> €</td>
                            <form method="POST">
                                <td>
                                    <input type="hidden" name="id_plat" value="<?= $id_plat ?>">
                                    <input type="number" name="quantite" value="<?= $quantite ?>" min="0" style="width:50px;">
                                </td>
                                <td><strong style="color:#5d7358;"><?= number_format($sous_total, 2) ?> €</strong></td>
                                <td><button type="submit" name="modifier_qte" class="btn-save-cmd">OK</button></td>
                            </form>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 style="margin: 20px 0;">Total : <?= number_format($total_commande, 2) ?> €</h3>

            <hr style="border:0; border-top:1px solid #eee; margin:40px 0;">

            <h3 class="france-ancien-livre" style="margin-bottom:25px;">Validation de la commande</h3>
            
            <form action="profil.php#commandes" method="POST">
                <div class="grille-options">
                    
                    <div class="colonne-choix">
                        <p><strong>Temps de préparation</strong></p>
                        <label class="option-label">
                            <input type="radio" name="type_preparation" value="immediat" checked>
                            Immédiate (dès que possible)
                        </label>
                        <label class="option-label">
                            <input type="radio" name="type_preparation" value="differe">
                            Différée à : 
                            <input type="time" name="heure_recuperation" style="border:1px solid #ddd; border-radius:4px;">
                        </label>
                    </div>

                    <div class="colonne-choix">
                        <p><strong>Mode de retrait</strong></p>
                        <label class="option-label">
                            <input type="radio" name="type_livraison" value="emporter" checked>
                            À emporter (restaurant)
                        </label>
                        <label class="option-label">
                            <input type="radio" name="type_livraison" value="livraison">
                            En livraison (domicile)
                        </label>
                    </div>

                </div>

                <div class="conteneur-validation">
                    <a href="#modal-paiement" class="btn-lien-paiement">
                        Confirmer et payer (<?= number_format($total_commande, 2) ?> €)
                    </a>
                </div>

                <div id="modal-paiement" class="modal-fond">
                    <div class="modal-contenu">
                        <h2>Paiement sécurisé</h2>
                        <hr>
                        <div class="infos-details">
                            <p>Vous allez être redirigé vers notre partenaire bancaire pour finaliser votre commande.</p>
                            <p>Total à régler : <strong><?= number_format($total_commande, 2) ?> €</strong></p>
                        </div>
                        
                        <button type="submit">Payer avec CY Bank</button>
                        
                        <button type="button"> 
                            <a href="#!" class="btn-fermer">Fermer</a> 
                        </button>
                    </div>
                </div>

            </form>

        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>