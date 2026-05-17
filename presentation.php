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
        $correspond_nom  = stripos($plat['nom'], $recherche) !== false;
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

<!-- Panneau filtres & tris -->
<div class="panneau-filtres">
    <details>
        <summary>Filtres &amp; Tris</summary>
        <div class="panneau-corps">
            <div class="filtres-grille">
                <div class="filtres-groupe">
                    <h4>Catégorie</h4>
                    <label><input type="checkbox" class="filtre-cat" value="Entrées / Apéro"> Entrées / Apéro</label>
                    <label><input type="checkbox" class="filtre-cat" value="Plats"> Plats</label>
                    <label><input type="checkbox" class="filtre-cat" value="Accompagnements"> Accompagnements</label>
                    <label><input type="checkbox" class="filtre-cat" value="Desserts"> Desserts</label>
                </div>
                <div class="filtres-groupe">
                    <h4>Régime</h4>
                    <label><input type="checkbox" class="filtre-tag" value="végétarien"> Végétarien</label>
                    <label><input type="checkbox" class="filtre-tag" value="vegan"> Vegan</label>
                    <label><input type="checkbox" class="filtre-tag" value="sans-gluten"> Sans gluten</label>
                    <label><input type="checkbox" class="filtre-tag" value="sans-lactose"> Sans lactose</label>
                    <label><input type="checkbox" class="filtre-tag" value="sans-oeuf"> Sans œuf</label>
                </div>
                <div class="filtres-groupe">
                    <h4>Goût</h4>
                    <label><input type="checkbox" class="filtre-tag" value="salé"> Salé</label>
                    <label><input type="checkbox" class="filtre-tag" value="sucré"> Sucré</label>
                    <label><input type="checkbox" class="filtre-tag" value="épicé"> Épicé</label>
                </div>
            </div>
            <hr class="tris-separateur">
            <div class="tris-ligne">
                <span>Trier :</span>
                <button class="btn-tri" data-tri="">Défaut</button>
                <button class="btn-tri" data-tri="prix-asc">Prix croissant ↑</button>
                <button class="btn-tri" data-tri="prix-desc">Prix décroissant ↓</button>
                <button class="btn-tri" data-tri="commandes">Plus commandés</button>
            </div>
        </div>
    </details>
</div>

<div id="zone-plats-filtrés">

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
                            <div class="menu-img" style="background-image:url('<?php echo htmlspecialchars($image_url); ?>');"></div>
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

</div><!-- #zone-plats-filtrés -->

<script>
const tousLesPlats = <?= json_encode(array_values($tous_les_plats), JSON_UNESCAPED_UNICODE) ?>;

let triActif = '';

function construireCartes(plats) {
    if (plats.length === 0) {
        return '<div class="msg-filtre-vide">Aucun plat ne correspond aux filtres sélectionnés.</div>';
    }

    // Grouper par catégorie en conservant l'ordre défini
    const ordreCategories = ['Entrées / Apéro', 'Plats', 'Accompagnements', 'Desserts'];
    const groupes = {};
    plats.forEach(p => {
        const cat = p.categorie || 'Plats';
        if (!groupes[cat]) groupes[cat] = [];
        groupes[cat].push(p);
    });

    let html = '';
    ordreCategories.forEach(cat => {
        if (!groupes[cat] || groupes[cat].length === 0) return;
        html += `<div class="categorie-placement"><span class="categorie-badge">${cat}</span></div>`;
        html += '<div class="ligne-menu">';
        groupes[cat].forEach(plat => {
            const img = plat.image || 'images/groin_de_folie.png';
            const prix = plat.prix.toFixed(2).replace('.', ',');
            const allergenes = (plat.informations?.allergenes?.length > 0)
                ? plat.informations.allergenes.join(', ')
                : 'Aucun';
            html += `
                <div class="menu-cadre">
                    <a href="#detail-plat-${plat.id}" class="lien-vers-modal">
                        <div class="container-img">
                            <div class="menu-img" style="background-image:url('${img}');"></div>
                        </div>
                        <div class="menu-contenu">
                            <div class="menu-titre">${plat.nom}</div>
                        </div>
                    </a>
                    <div class="menu-bas-carte">
                        <form action="ajouter_panier.php" method="POST" class="form-achat">
                            <input type="hidden" name="id_plat" value="${plat.id}">
                            <div class="ligne-prix-quantite">
                                <div class="selecteur-quantite">
                                    <input type="number" name="quantite" value="1" min="1" max="10">
                                </div>
                                <div class="prix-display">${prix} €</div>
                            </div>
                            <button type="submit" class="btn-ajouter-vert">Ajouter</button>
                        </form>
                    </div>
                </div>
                <div id="detail-plat-${plat.id}" class="modal-fond">
                    <div class="modal-contenu">
                        <h3 class="modal-titre-plat">${plat.nom}</h3>
                        <p class="modal-categorie-plat">${plat.categorie}</p>
                        <hr>
                        <img src="${img}" alt="${plat.nom}" class="modal-image-plat">
                        <p>${plat.description}</p>
                        <div class="paiement">
                            <p><strong>Nutrition :</strong> ${plat.informations?.nutritionnelles || 'Non renseigné'}</p>
                            <p><strong>Allergènes :</strong> ${allergenes}</p>
                        </div>
                        <p class="modal-prix-plat">Prix : ${prix} €</p>
                        <a href="#!" class="btn-lien-paiement">Fermer</a>
                    </div>
                </div>`;
        });
        html += '</div>';
    });
    return html;
}

function appliquerFiltres() {
    const categories = [...document.querySelectorAll('.filtre-cat:checked')].map(el => el.value);
    const filtres    = [...document.querySelectorAll('.filtre-tag:checked')].map(el => el.value);
    const aucunFiltre = categories.length === 0 && filtres.length === 0;

    if (aucunFiltre && triActif === '') {
        document.getElementById('zone-plats-filtrés').innerHTML = construireCartes([...tousLesPlats]);
        return;
    }

    if (aucunFiltre && triActif !== '') {
        let plats = [...tousLesPlats];
        trierCoteClient(plats);
        document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(plats);
        return;
    }

    fetch('includes/filtres_plats.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ categories, filtres, tri: triActif })
    })
    .then(r => r.json())
    .then(plats => {
        if (triActif === 'prix-asc') plats.sort((a, b) => a.prix - b.prix);
        else if (triActif === 'prix-desc') plats.sort((a, b) => b.prix - a.prix);
        document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(plats);
    });
}

function trierCoteClient(plats) {
    if (triActif === 'prix-asc')  plats.sort((a, b) => a.prix - b.prix);
    if (triActif === 'prix-desc') plats.sort((a, b) => b.prix - a.prix);
}

document.querySelectorAll('.filtre-cat, .filtre-tag').forEach(el => {
    el.addEventListener('change', appliquerFiltres);
});

document.querySelectorAll('.btn-tri').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.btn-tri').forEach(b => b.classList.remove('actif'));
        btn.classList.add('actif');
        triActif = btn.dataset.tri;

        const categories = [...document.querySelectorAll('.filtre-cat:checked')].map(el => el.value);
        const filtres    = [...document.querySelectorAll('.filtre-tag:checked')].map(el => el.value);

        if (triActif === '') {
            document.querySelectorAll('.filtre-cat, .filtre-tag').forEach(cb => cb.checked = false);
            let plats = [...tousLesPlats];
            document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(plats);
            return;
        }

        const aucunFiltre = categories.length === 0 && filtres.length === 0;

        if (aucunFiltre && (triActif === 'prix-asc' || triActif === 'prix-desc')) {
            let plats = [...tousLesPlats];
            trierCoteClient(plats);
            document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(plats);
        } else {
            fetch('includes/filtres_plats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ categories, filtres, tri: triActif })
            })
            .then(r => r.json())
            .then(plats => {
                document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(plats);
            });
        }
    });
});

document.querySelector('.btn-tri[data-tri=""]').classList.add('actif');
</script>

<?php include 'includes/footer.php'; ?>
