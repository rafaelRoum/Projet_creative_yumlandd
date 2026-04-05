<?php 
session_start(); 
require_once 'includes/fonctions.php'; 

$titre_page = "Présentation - Le Groin de Folie";
include 'includes/header.php';

$tous_les_plats = get_plats();

$recherche = isset($_GET['q']) ? trim($_GET['q']) : '';

$categories_menu = [
    'Entrées / Apéro' => [],
    'Plats' => [],
    'Accompagnements' => [],
    'Desserts' => []
];

$resultats_trouves = 0;
foreach ($tous_les_plats as $plat) {
    if ($recherche !== '') {
        $correspond_nom = stripos($plat['nom'], $recherche) !== false;
        $correspond_desc = stripos($plat['description'], $recherche) !== false;
        if (!$correspond_nom && !$correspond_desc) continue; 
    }

    $cat = $plat['categorie'] ?? 'Plats'; 
    if (array_key_exists($cat, $categories_menu)) {
        $categories_menu[$cat][] = $plat;
        $resultats_trouves++;
    }
}
?>
    
<div class="recherche-placement">
    <form action="presentation.php" method="GET" class="barre-recherche center-grid">
        <input type="text" name="q" value="<?= htmlspecialchars($recherche) ?>" placeholder="Rechercher un plat, une envie..." />
        <button type="submit">Rechercher</button>
    </form>
</div>

<?php if ($recherche !== '' && $resultats_trouves === 0): ?>
    <meta http-equiv="refresh" content="1;url=presentation.php">
    <div class="categorie-placement placement-centre margin-bot" style="margin-bottom:22%">
        <span class="categorie-badge badge-erreur">Aucun résultat pour "<?= htmlspecialchars($recherche) ?>"</span>
    </div>
<?php endif; ?>

<?php foreach ($categories_menu as $nom_categorie => $plats_de_la_categorie): ?>
    <?php if (!empty($plats_de_la_categorie)): ?>
        
        <div class="categorie-placement">
            <span class="categorie-badge"><?= $nom_categorie ?></span>
        </div>

        <div class="ligne-menu">
            <?php foreach ($plats_de_la_categorie as $plat): 
                $image_url = $plat['image'] ?? 'images/groin_de_folie.png'; 
            ?>
                
                <div class="menu-cadre">
                    <a href="#detail-plat-<?= $plat['id'] ?>" class="lien-vers-modal">
                        <div class="container-img">
                            <div class="menu-img"  style="background-image:url('<?php echo htmlspecialchars($image_url); ?>');"></div>
                        </div>
                        <div class="menu-contenu">
                            <div class="menu-titre"><?= htmlspecialchars($plat['nom']) ?></div>
                        </div>
                    </a>

                    <div class="menu-bas-carte">
                        <form action="ajouter_panier.php" method="POST" class="form-achat">
                            <input type="hidden" name="id_plat" value="<?= $plat['id'] ?>">
                            
                            <div class="ligne-prix-quantite">
                                <div class="selecteur-quantite">
                                    <input type="number" name="quantite" value="1" min="1" max="10">
                                </div>
                                <div class="prix-display"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</div>
                            </div>

                            <button type="submit" class="btn-ajouter-vert">Ajouter</button>
                        </form>
                    </div>
                </div>

                <div id="detail-plat-<?= $plat['id'] ?>" class="modal-fond">
                    <div class="modal-contenu">
                        <h3 class="modal-titre-plat"><?= htmlspecialchars($plat['nom']) ?></h3>
                        <p class="modal-categorie-plat"><?= htmlspecialchars($plat['categorie']) ?></p>
                        <hr>
                        <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($plat['nom']) ?>" class="modal-image-plat">
                        <p><?= htmlspecialchars($plat['description']) ?></p>

                        <div class="paiement">
                            <p><strong>Nutrition :</strong> <?= $plat['informations']['nutritionnelles'] ?? 'Non renseigné' ?></p>
                            <p><strong>Allergènes :</strong> 
                                <?= !empty($plat['informations']['allergenes']) ? htmlspecialchars(implode(', ', $plat['informations']['allergenes'])) : "Aucun" ?>
                            </p>
                        </div>
                        <p class="modal-prix-plat">Prix : <?= number_format($plat['prix'], 2, ',', ' ') ?> €</p>
                        <a href="#!" class="btn-lien-paiement">Fermer</a> 
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>