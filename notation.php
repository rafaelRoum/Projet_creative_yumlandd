<?php session_start(); ?>

<?php
$titre_page = "Notation - Le Groin de Folie";
include 'includes/header.php';
?>

<section class="place-cadre">
    <div class="cadre">
        <h2>Notation de votre commande</h2>
        <div class="formulaire">
            <label>Qualité de la livraison</label>
            <div class="etoile" data-type="livraison">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>
        </div>
        <div class="formulaire">
            <label>Qualité des produits</label>
            <div class="etoile" data-type="produits">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>
        </div>
        <div class="formulaire">
            <label for="comment">Commentaire (optionnel)</label>
            <textarea id="commentaire" placeholder="Votre avis..."></textarea>
        </div>
        <button class="envoi-notation">Envoyer ma note</button>
    </div>
</section>

<?php
include 'includes/footer.php';
?>