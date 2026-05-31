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

// Vérification de la connexion de l'utilisateur
$est_connecte = isset($_SESSION['id']);
?>



<div class="recherche-placement">
    <form action="presentation.php" method="GET" id="form-barre-recherche" class="barre-recherche center-grid">
        <input type="text" id="input-recherche" name="q" value="<?= htmlspecialchars($recherche) ?>" placeholder="Rechercher un plat, une envie..." autocomplete="off" />
    </form>
</div>

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
                                    <input type="number" name="quantite" value="1" min="1" max="10" <?= !$est_connecte ? 'disabled style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
                                </div>
                                <div class="prix-display"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</div>
                            </div>
                            <?php if ($est_connecte): ?>
                                <button type="submit" class="btn-ajouter-vert">Ajouter</button>
                            <?php else: ?>
                                <button type="button" onclick="window.location.href='connexion.php'" class="btn-ajouter-vert" style="background-color: #6c757d; font-size: 11px;">Se connecter</button>
                            <?php endif; ?>
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

</div><script>
const estConnecte = <?= $est_connecte ? 'true' : 'false' ?>;
const tousLesPlats = <?= json_encode(array_values($tous_les_plats), JSON_UNESCAPED_UNICODE) ?>;

let triActif = '';

function construireCartes(plats) {
    if (plats.length === 0) {
        return '<div class="msg-filtre-vide">Aucun plat ne correspond aux critères sélectionnés.</div>';
    }

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
            const allergenes = (plat.informations?.allergenes?.length > 0) ? plat.informations.allergenes.join(', ') : 'Aucun';
            
            const inputAttr = !estConnecte ? 'disabled style="opacity:0.6; cursor:not-allowed;"' : '';
            const btnHtml = estConnecte 
                ? '<button type="submit" class="btn-ajouter-vert">Ajouter</button>'
                : '<button type="button" onclick="window.location.href=\'connexion.php\'" class="btn-ajouter-vert" style="background-color: #6c757d; font-size: 11px;">Se connecter</button>';

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
                                    <input type="number" name="quantite" value="1" min="1" max="10" ${inputAttr}>
                                </div>
                                <div class="prix-display">${prix} €</div>
                            </div>
                            ${btnHtml}
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
    const recherche  = document.getElementById('input-recherche').value.trim().toLowerCase();

    // 1. Filtrage côté client complet (Intègre les cases à cocher ET la barre de recherche)
    let platsFiltrés = tousLesPlats.filter(plat => {
        // Condition recherche textuelle
        if (recherche !== '') {
            const nomMatch = plat.nom.toLowerCase().includes(recherche);
            const descMatch = plat.description.toLowerCase().includes(recherche);
            if (!nomMatch && !descMatch) return false;
        }
        return true;
    });

    const aucunFiltreCase = categories.length === 0 && filtres.length === 0;

    // 2. Si des filtres avancés (tags) sont cochés, on croise avec le script serveur
    if (!aucunFiltreCase) {
        fetch('includes/filtres_plats.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ categories, filtres, tri: triActif })
        })
        .then(r => r.json())
        .then(platsDuServeur => {
            // On ne garde que les plats du serveur qui matchent aussi la saisie clavier actuelle
            const idsServeur = platsDuServeur.map(p => p.id);
            platsFiltrés = platsFiltrés.filter(p => idsServeur.includes(p.id));
            
            trierCoteClient(platsFiltrés);
            document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(platsFiltrés);
        });
    } else {
        // Pas de filtres cochés, rendu direct ultra-rapide
        trierCoteClient(platsFiltrés);
        document.getElementById('zone-plats-filtrés').innerHTML = construireCartes(platsFiltrés);
    }
}

function trierCoteClient(plats) {
    if (triActif === 'prix-asc')  plats.sort((a, b) => a.prix - b.prix);
    if (triActif === 'prix-desc') plats.sort((a, b) => b.prix - a.prix);
}

// Écouteur sur la barre de recherche pour un filtrage en temps réel à chaque lettre tapée
document.getElementById('input-recherche').addEventListener('input', appliquerFiltres);

// Désactive le comportement de rechargement classique lors de la soumission de la recherche
document.getElementById('form-barre-recherche').addEventListener('submit', function(e) {
    e.preventDefault();
    appliquerFiltres();
});

document.querySelectorAll('.filtre-cat, .filtre-tag').forEach(el => {
    el.addEventListener('change', appliquerFiltres);
});

document.querySelectorAll('.btn-tri').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.btn-tri').forEach(b => b.classList.remove('actif'));
        btn.classList.add('actif');
        triActif = btn.dataset.tri;

        if (triActif === '') {
            document.querySelectorAll('.filtre-cat, .filtre-tag').forEach(cb => cb.checked = false);
            document.getElementById('input-recherche').value = '';
        }
        appliquerFiltres();
    });
});

document.querySelector('.btn-tri[data-tri=""]').classList.add('actif');

// Interception AJAX pour ajouter au panier et actualiser le compteur du header
document.addEventListener('submit', function (e) {
    if (e.target && e.target.classList.contains('form-achat')) {
        
        if (!estConnecte) {
            e.preventDefault();
            window.location.href = 'connexion.php';
            return;
        }

        e.preventDefault();

        const form = e.target;
        const btnSubmit = form.querySelector('button[type="submit"]');
        const texteOriginal = btnSubmit.textContent;
        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData
        })
        .then(response => {
            if (!response.headers.get('content-type')?.includes('application/json')) {
                return { success: true, fallback: true };
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const liensNav = document.querySelectorAll('header a, .nav-link');
                let lienPanier = null;

                liensNav.forEach(el => {
                    if (el.textContent.includes('Panier')) {
                        lienPanier = el;
                    }
                });

                if (lienPanier) {
                    if (data.fallback) {
                        const nombres = lienPanier.textContent.match(/\d+/);
                        const qteActuelle = nombres ? parseInt(nombres[0]) : 0;
                        const qteAjoutee = parseInt(form.querySelector('input[name="quantite"]').value) || 1;
                        lienPanier.textContent = `Panier (${qteActuelle + qteAjoutee})`;
                    } else {
                        lienPanier.textContent = `Panier (${data.nouveau_total})`;
                    }
                }

                btnSubmit.textContent = "✓";
                btnSubmit.style.backgroundColor = "#28a745";
                btnSubmit.style.color = "#ffffff";

                setTimeout(() => {
                    btnSubmit.textContent = texteOriginal;
                    btnSubmit.style.backgroundColor = "";
                    btnSubmit.style.color = "";
                }, 1500);
            } else {
                alert(data.message || "Erreur lors de l'ajout au panier.");
            }
        })
        .catch(error => {
            console.error("Erreur:", error);
            alert("Erreur de communication avec le serveur.");
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>