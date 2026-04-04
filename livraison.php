<?php session_start(); ?>

<?php
$titre_page = "Livraison - Le Groin de Folie";
include 'includes/header.php';
?>

<div class="livraison-placement">    
    <div class="livraison-cadre">
        <div class="entete-livraison">
            <h2>Commande #4092</h2>
            <span class="statut-badge statut-en-attente">À livrer</span>
        </div>
        <div class="livraison-info">
            <h3>📍 Adresse du client</h3>
            <p class="addresse-info">12 rue du Jambon<br>75001 Paris</p>
            <div class="details-client">
                <div class="detail-info"><strong>👤 Client :</strong> Justin Mason</div>
                <div class="detail-info"><strong>🏢 Étage :</strong> 3ème (Porte Gauche)</div>
                <div class="detail-info"><strong>🔑 Interphone :</strong> B459</div>
                <div class="detail-info"><strong>📞 Téléphone :</strong> <a href="tel:+33612345678">06 12 34 56 78</a></div>
            </div>
            <div class="commentaire-bloc">
                <strong>📝 Instructions spécifiques :</strong>
                <p>"Le code de la grille extérieure est 1234. Merci de ne pas sonner à cause du bébé, appelez-moi quand vous êtes en bas."</p>
            </div>
        </div>
        <div class="livraison-actions">
            <a href="https://maps.google.com/?q=12+rue+du+Jambon+75001+Paris" target="_blank" class="nav-btn">
                🗺️ Ouvrir dans le GPS
            </a>
            <button class="livrer-btn">
                ✅ Livraison Terminée
            </button>
        </div>
    </div>
    <div class="livraison-cadre"> 
        <div class="entete-livraison">
            <h2>Commande #4093</h2>
            <span class="statut-badge statut-en-attente">À livrer</span>
        </div>
        <div class="livraison-info">
            <h3>📍 Adresse du client</h3>
            <p class="addresse-info">45 avenue des Porcelets<br>75002 Paris</p>     
            <div class="details-client">
                <div class="detail-info"><strong>👤 Client :</strong> Marie Lecoq</div>
                <div class="detail-info"><strong>🏢 Étage :</strong> RDC</div>
                <div class="detail-info"><strong>🔑 Interphone :</strong> Sans</div>
                <div class="detail-info"><strong>📞 Téléphone :</strong> <a href="tel:+33698765432">06 98 76 54 32</a></div>
            </div>
            <div class="commentaire-bloc">
                <strong>📝 Instructions spécifiques :</strong>
                <p>"Porte rouge au fond de la cour."</p>
            </div>
        </div>
        <div class="livraison-actions">
            <a href="https://maps.google.com/?q=45+avenue+des+Porcelets+75001+Paris" target="_blank" class="nav-btn">
                🗺️ Ouvrir dans le GPS
            </a>           
            <button class="livrer-btn">
                ✅ Livraison Terminée
            </button>
        </div>       
    </div>
</div>

<?php
include 'includes/footer.php';
?>