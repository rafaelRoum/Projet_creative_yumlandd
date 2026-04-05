<?php session_start(); ?>

<?php
$titre_page = "index - Le Groin de Folie";
include 'includes/header.php';
?>

<div class="entete">
    <div class="entete-gauche">
        <a href="presentation.php">
            <img src="images/groin_de_folie_icons.png" alt="Logo Accueil" class="entete-logo">
        </a>
    </div>    
    <h1 class="france-ancien-livre entete-titre">Le Groin de Folie</h1>
    <div class="entete-droite">
    </div>
</div>

<div class="categorie-placement placement-centre">
    <span class="categorie-badge">🌟 Plat du jour</span>
</div>
<div class="ligne-menu menu-centre">
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/quiche.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Quiche Maison</div>
            <div class="menu-prix">9 €</div>
        </div>
    </div>      
    <div class="menu-cadre chef-recommandation">
        <p class="chef-texte">👨‍🍳 Recommandation du chef : <br><br>Aujourd'hui, laissez-vous tenter par notre délicieuse quiche sortie du four ce matin. Parfaitement dorée et croustillante !</p>
    </div>
</div>

<div class="categorie-placement placement-centre">
    <span class="categorie-badge">🔥 Fréquemment commandés</span>
</div>
<div class="ligne-menu menu-centre">
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/charcuterie.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Charcuterie</div>
            <div class="menu-prix">9 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/fondant.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Fondant</div>
            <div class="menu-prix">8 €</div>
        </div>
    </div>
    <div class="menu-cadre">
        <div class="menu-img" style="background-image:url('images/saumon_fume.png')"></div>
        <div class="menu-contenu">
            <div class="menu-titre">Saumon Fumé</div>
            <div class="menu-prix">12 €</div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>