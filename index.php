<?php
session_start();
require_once 'includes/fonctions.php';

$titre_page = "Accueil - Le Groin de Folie";
include 'includes/header.php';

$tous_les_plats = get_plats();
$plats_index = [];
foreach ($tous_les_plats as $p) { $plats_index[$p['id']] = $p; }

// Plat du jour (Quiche Maison, id=2)
$plat_du_jour = $plats_index[2] ?? $tous_les_plats[0] ?? null;

// Les plus commandés : compter depuis commandes.json
$commandes_json = file_exists('data/commandes.json')
    ? json_decode(file_get_contents('data/commandes.json'), true) ?? []
    : [];

$compteur = [];
foreach ($commandes_json as $cmd) {
    foreach ($cmd['contenu'] as $item) {
        if ($item['type'] === 'plat') {
            $compteur[$item['id_item']] = ($compteur[$item['id_item']] ?? 0) + 1;
        }
    }
}
arsort($compteur);
$top_ids = array_slice(array_keys($compteur), 0, 3);

// Si pas encore de commandes, afficher 3 plats en vedette
if (empty($top_ids)) {
    $top_ids = [1, 12, 5]; // Charcuterie, Fondant, Saumon
}

$plats_populaires = array_filter(array_map(fn($id) => $plats_index[$id] ?? null, $top_ids));
?>

<div class="entete">
    <div class="entete-gauche">
        <a href="presentation.php">
            <img id="icons" src="images/groin_de_folie_icons.png" alt="Logo Accueil" class="entete-logo">
        </a>
    </div>
    <h1 class="france-ancien-livre entete-titre">Le Groin de Folie</h1>
    <div class="entete-droite"></div>
</div>

<!-- Plat du jour -->
<div class="categorie-placement placement-centre">
    <span class="categorie-badge">🌟 Plat du jour</span>
</div>

<div class="ligne-menu menu-centre">
    <?php if ($plat_du_jour): ?>
    <div class="menu-cadre">
        <a href="presentation.php" class="lien-vers-modal">
            <div class="container-img">
                <div class="menu-img" style="background-image:url('<?= htmlspecialchars($plat_du_jour['image'] ?? 'images/groin_de_folie.png') ?>')"></div>
            </div>
            <div class="menu-contenu">
                <div class="menu-titre"><?= htmlspecialchars($plat_du_jour['nom']) ?></div>
            </div>
        </a>
        <div class="menu-bas-carte">
            <form action="ajouter_panier.php" method="POST" class="form-achat">
                <input type="hidden" name="id_plat" value="<?= $plat_du_jour['id'] ?>">
                <div class="ligne-prix-quantite">
                    <div class="selecteur-quantite">
                        <input type="number" name="quantite" value="1" min="1" max="10">
                    </div>
                    <div class="prix-display"><?= number_format($plat_du_jour['prix'], 2, ',', ' ') ?> €</div>
                </div>
                <button type="submit" class="btn-ajouter-vert">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="menu-cadre chef-recommandation" style="display:flex; align-items:center; padding:20px;">
        <p class="chef-texte">👨‍🍳 Recommandation du chef :<br><br>Aujourd'hui, laissez-vous tenter par notre délicieuse <?= htmlspecialchars($plat_du_jour['nom']) ?> sortie du four ce matin. Parfaitement dorée et croustillante !</p>
    </div>
    <?php endif; ?>
</div>

<!-- Fréquemment commandés -->
<div class="categorie-placement placement-centre">
    <span class="categorie-badge">🔥 Fréquemment commandés</span>
</div>

<div class="ligne-menu menu-centre">
    <?php foreach ($plats_populaires as $plat): ?>
    <div class="menu-cadre">
        <a href="presentation.php" class="lien-vers-modal">
            <div class="container-img">
                <div class="menu-img" style="background-image:url('<?= htmlspecialchars($plat['image'] ?? 'images/groin_de_folie.png') ?>')"></div>
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
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
