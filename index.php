<?php
session_start();
require_once 'includes/fonctions.php';

$titre_page = "Accueil - Le Groin de Folie";
include 'includes/header.php';


$tous_les_plats = get_plats() ?? [];
$plats_index = [];
foreach ($tous_les_plats as $p) { 
    $plats_index[$p['id']] = $p; 
}


$plat_du_jour = null;
if (isset($plats_index[2])) {
    $plat_du_jour = $plats_index[2];
} elseif (!empty($tous_les_plats)) {
    $plat_du_jour = $tous_les_plats[0];
}


$commandes_json = file_exists('data/commandes.json')
    ? json_decode(file_get_contents('data/commandes.json'), true) ?? []
    : [];

$compteur = [];
foreach ($commandes_json as $cmd) {
    if (isset($cmd['contenu']) && is_array($cmd['contenu'])) {
        foreach ($cmd['contenu'] as $item) {
            if (isset($item['type']) && $item['type'] === 'plat') {
                $id = $item['id_item'] ?? null;
                if ($id) {
                    $compteur[$id] = ($compteur[$id] ?? 0) + 1;
                }
            }
        }
    }
}
arsort($compteur);
$top_ids = array_slice(array_keys($compteur), 0, 3);

if (empty($top_ids)) {
    $top_ids = [1, 12, 5]; 
}

$plats_populaires = array_filter(array_map(fn($id) => $plats_index[$id] ?? null, $top_ids));

$est_connecte = isset($_SESSION['id']);

// Chargement notations sécurisé
$notations_json = file_exists('data/notations.json')
    ? json_decode(file_get_contents('data/notations.json'), true) ?? []
    : [];

$total_produits  = 0; $nb_produits  = 0;
$total_livraison = 0; $nb_livraison = 0;
foreach ($notations_json as $n) {
    if (isset($n['note_produits'])) {
        $total_produits += $n['note_produits']; 
        $nb_produits++;
    }
    if (isset($n['note_livraison']) && $n['note_livraison'] !== null) {
        $total_livraison += $n['note_livraison']; 
        $nb_livraison++;
    }
}
$moy_produits  = $nb_produits  > 0 ? round($total_produits  / $nb_produits,  1) : null;
$moy_livraison = $nb_livraison > 0 ? round($total_livraison / $nb_livraison, 1) : null;
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

<div class="categorie-placement placement-centre">
    <span class="categorie-badge">Plat du jour</span>
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
                        <input type="number" name="quantite" value="1" min="1" max="10" <?= !$est_connecte ? 'disabled style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
                    </div>
                    <div class="prix-display"><?= number_format($plat_du_jour['prix'], 2, ',', ' ') ?> €</div>
                </div>
                <?php if ($est_connecte): ?>
                    <button type="submit" class="btn-ajouter-vert">Ajouter</button>
                <?php else: ?>
                    <button type="button" onclick="window.location.href='connexion.php'" class="btn-ajouter-vert" style="background-color: #6c757d; font-size: 11px;">Se connecter</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="menu-cadre chef-recommandation" style="display:flex; align-items:center; padding:20px;">
        <p class="chef-texte">Recommandation du chef :<br><br>Aujourd'hui, laissez-vous tenter par notre délicieuse <?= htmlspecialchars($plat_du_jour['nom']) ?> sortie du four ce matin. Parfaitement dorée et croustillante !</p>
    </div>
    <?php endif; ?>
</div>

<div class="categorie-placement placement-centre">
    <span class="categorie-badge">Fréquemment commandés</span>
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
    <?php endforeach; ?>
</div>

<script>
const estConnecte = <?= $est_connecte ? 'true' : 'false' ?>;

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
        .then(response => response.headers.get('content-type')?.includes('application/json') ? response.json() : { success: true, fallback: true })
        .then(data => {
            if (data.success) {
                const liensNav = document.querySelectorAll('header a, .nav-link');
                liensNav.forEach(el => {
                    if (el.textContent.includes('Panier')) {
                        let text = el.textContent.trim();
                        if (text.startsWith('Panier')) {
                            const nombres = text.match(/\d+/);
                            const qteActuelle = nombres ? parseInt(nombres[0]) : 0;
                            const qteAjoutee = parseInt(form.querySelector('input[name="quantite"]').value) || 1;
                            el.textContent = `Panier (${qteActuelle + qteAjoutee})`;
                        }
                    }
                });
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

<div class="categorie-placement placement-centre" style="margin-top:40px;">
    <span class="categorie-badge">Avis de nos clients</span>
</div>

<?php if (!empty($notations_json)): ?>
<div style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap; margin:0 auto 50px; padding:0 20px;">
    <?php
    function etoiles_html($note) {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $note >= $i ? '<span style="color:#d4a017;">★</span>' : '<span style="color:#ddd;">★</span>';
        }
        return $html;
    }
    ?>
    <div style="text-align:center; background:#fff; border-radius:12px; padding:25px 40px; box-shadow:0 2px 10px rgba(0,0,0,0.08);">
        <p style="margin:0 0 8px; font-size:13px; color:#888; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">Qualité des produits</p>
        <div style="font-size:30px; letter-spacing:4px;"><?php echo etoiles_html($moy_produits ?? 0); ?></div>
        <p style="margin:8px 0 0; font-size:24px; font-weight:bold; color:#5d7358;"><?php echo $moy_produits ?? '-'; ?> <span style="font-size:14px; color:#aaa;">/ 5</span></p>
        <p style="margin:4px 0 0; font-size:12px; color:#bbb;"><?php echo $nb_produits; ?> avis</p>
    </div>
    <?php if ($moy_livraison !== null): ?>
    <div style="text-align:center; background:#fff; border-radius:12px; padding:25px 40px; box-shadow:0 2px 10px rgba(0,0,0,0.08);">
        <p style="margin:0 0 8px; font-size:13px; color:#888; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">Qualité de la livraison</p>
        <div style="font-size:30px; letter-spacing:4px;"><?php echo etoiles_html($moy_livraison); ?></div>
        <p style="margin:8px 0 0; font-size:24px; font-weight:bold; color:#5d7358;"><?php echo $moy_livraison; ?> <span style="font-size:14px; color:#aaa;">/ 5</span></p>
        <p style="margin:4px 0 0; font-size:12px; color:#bbb;"><?php echo $nb_livraison; ?> avis</p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>