<?php 
session_start(); 
require_once 'includes/fonctions.php'; 

$titre_page = "Présentation - Le Groin de Folie";
include 'includes/header.php';

// 1. Récupération de tous les plats
$tous_les_plats = get_plats();

// 2. Gestion de la recherche
$recherche = isset($_GET['q']) ? trim($_GET['q']) : '';

// 3. Initialisation des catégories
$categories_menu = [
    'Entrées / Apéro' => [],
    'Plats' => [],
    'Accompagnements' => [],
    'Desserts' => []
];

// 4. Répartition dans les catégories (avec filtrage si une recherche est en cours)
$resultats_trouves = 0;
foreach ($tous_les_plats as $plat) {
    if ($recherche !== '') {
        $correspond_nom = stripos($plat['nom'], $recherche) !== false;
        $correspond_desc = stripos($plat['description'], $recherche) !== false;
        
        if (!$correspond_nom && !$correspond_desc) {
            continue; 
        }
    }

    $cat = isset($plat['categorie']) ? $plat['categorie'] : 'Plats'; 
    if (array_key_exists($cat, $categories_menu)) {
        $categories_menu[$cat][] = $plat;
        $resultats_trouves++;
    }
}
?>
    
<div class="recherche-placement">
    <form action="presentation.php" method="GET" class="barre-recherche center-grid">
        <input type="text" name="q" value="<?php echo htmlspecialchars($recherche); ?>" placeholder="Rechercher un plat, une envie..." />
        <button type="submit">Rechercher</button>
    </form>
</div>

<?php if ($recherche !== '' && $resultats_trouves === 0): ?>
    <div class="categorie-placement">
        <span class="categorie-badge badge-erreur">Aucun résultat pour "<?php echo htmlspecialchars($recherche); ?>"</span>
    </div>
    <div class="message-erreur-recherche">
        <a href="presentation.php">Retour à la carte complète</a>
    </div>
<?php endif; ?>

<?php foreach ($categories_menu as $nom_categorie => $plats_de_la_categorie): ?>
    
    <?php if (!empty($plats_de_la_categorie)): ?>
        
        <div class="categorie-placement">
            <span class="categorie-badge"><?php echo $nom_categorie; ?></span>
        </div>

        <div class="ligne-menu">
            <?php foreach ($plats_de_la_categorie as $plat): ?>
                <?php 
                $image_url = isset($plat['image']) ? $plat['image'] : 'images/groin_de_folie.png'; 
                ?>
                <div class="menu-cadre">
                    <a href="#detail-plat-<?php echo $plat['id']; ?>" class="lien-carte-entiere"></a>
                    <div class="menu-img" style="background-image:url('<?php echo htmlspecialchars($image_url); ?>')"></div>
                    <div class="menu-contenu">
                        <div class="menu-titre"><?php echo htmlspecialchars($plat['nom']); ?></div>
                        <div class="menu-prix"><?php echo number_format($plat['prix'], 2, ',', ' '); ?> €</div>
                        
                        <div class="form-ajout-groupe">
                            <form action="ajouter_panier.php" method="POST" class="form-inline-marge">
                                <input type="hidden" name="id_plat" value="<?php echo $plat['id']; ?>">
                                <input type="number" name="quantite" value="1" min="1" max="10" class="input-quantite-mini">
                                <button type="submit" class="btn-ajouter">Ajouter</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="detail-plat-<?php echo $plat['id']; ?>" class="modal-fond">
                    <div class="modal-contenu">
                        <h3 class="modal-titre-plat"><?php echo htmlspecialchars($plat['nom']); ?></h3>
                        <p class="modal-categorie-plat"><?php echo htmlspecialchars($plat['categorie']); ?></p>
                        <hr>
                        
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($plat['nom']); ?>" class="modal-image-plat">
                        
                        <p><?php echo htmlspecialchars($plat['description']); ?></p>

                        <div class="paiement">
                            <p><strong>Nutrition :</strong> <?php echo isset($plat['informations']['nutritionnelles']) ? htmlspecialchars($plat['informations']['nutritionnelles']) : 'Non renseigné'; ?></p>
                            <p><strong>Allergènes :</strong> 
                                <?php 
                                if (!empty($plat['informations']['allergenes'])) {
                                    echo htmlspecialchars(implode(', ', $plat['informations']['allergenes']));
                                } else {
                                    echo "Aucun";
                                }
                                ?>
                            </p>
                        </div>

                        <p class="modal-prix-plat">Prix : <?php echo number_format($plat['prix'], 2, ',', ' '); ?> €</p>

                            <a href="#!" class="btn-lien-paiement">Fermer</a> 
                    </div>
                </div>
                <?php endforeach; ?>
        </div>

    <?php endif; ?>

<?php endforeach; ?>

<?php
include 'includes/footer.php';
?>