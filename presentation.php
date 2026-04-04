<?php 
session_start(); 


require_once 'includes/fonctions.php'; 
$titre_page = "Présentation - Le Groin de Folie";
include 'includes/header.php';


$tous_les_plats = get_plats();
$categories_menu = [
    'Entrées / Apéro' => [],
    'Plats' => [],
    'Accompagnements' => [],
    'Desserts' => []
];

foreach ($tous_les_plats as $plat) {
    $cat = isset($plat['categorie']) ? $plat['categorie'] : 'Plats'; 
    if (array_key_exists($cat, $categories_menu)) {
        $categories_menu[$cat][] = $plat;
    }
}
?>
    
<div class="recherche-placement">
    <form class="barre-recherche center-grid">
        <input type="text" placeholder="Rechercher un plat, une envie..." />
        <button type="submit">Rechercher</button>
    </form>
</div>

<?php foreach ($categories_menu as $nom_categorie => $plats_de_la_categorie): ?>
    
    <?php if (!empty($plats_de_la_categorie)): ?>
        
        <div class="categorie-placement">
            <span class="categorie-badge"><?php echo $nom_categorie; ?></span>
        </div>

        <div class="ligne-menu">
            <?php foreach ($plats_de_la_categorie as $plat): ?>
                <?php 
                // Image de base si pas dans le json
                $image_url = isset($plat['image']) ? $plat['image'] : 'images/groin_de_folie.png'; 
                ?>
                <div class="menu-cadre">
                    <div class="menu-img" style="background-image:url('<?php echo htmlspecialchars($image_url); ?>')"></div>
                    <div class="menu-contenu">
                        <div class="menu-titre"><?php echo htmlspecialchars($plat['nom']); ?></div>
                        <div class="menu-prix"><?php echo number_format($plat['prix'], 2, ',', ' '); ?> €</div>
                        <div class="form-ajout-panier">
                        <form action="ajouter_panier.php" method="POST" class="form-ajout-panier">
                            <input type="hidden" name="id_plat" value="<?php echo $plat['id']; ?>">
                            <input type="number" name="quantite" value="1" min="1" max="10" class="input-quantite">
                            <button type="submit" class="btn-ajouter">Ajouter</button>
                        </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

<?php endforeach; ?>

<?php
include 'includes/footer.php';
?>