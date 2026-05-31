<footer>
    <div class="footer-fond">
        <div class="footer-col">
            <h3>Navigation</h3>
            <?php if (!isset($_SESSION['role'])): ?>
                <a href="index.php">Accueil</a>
                <a href="presentation.php">Présentation</a>
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">Inscription</a>
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php">Accueil</a>
                <a href="presentation.php">Présentation</a>
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">Inscription</a>
                <a href="profil.php">Profil</a>
            <?php elseif ($_SESSION['role'] === 'restaurateur' || $_SESSION['role'] === 'livreur' || $_SESSION['role'] === 'client'): ?>
                <a href="index.php">Accueil</a>
                <a href="presentation.php">Présentation</a>
                <a href="profil.php">Mon Profil</a>
            <?php endif; ?>
        </div>
        <div class="footer-col">
            <h3>&nbsp;</h3>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="commande.php">Commande</a>
                <a href="livraison.php">Livraison</a>
                <a href="administrateur.php">Admin</a>
                <a href="panier.php">Panier</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'restaurateur'): ?>
                <a href="commandes.php">Commandes à préparer</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'livreur'): ?>
                <a href="livraison.php">Commande en cours</a>
            <?php endif; ?>
        </div>
        <div class="footer-col">
            <h3>Contact</h3>
            <a href="https://maps.google.com/?q=12+rue+du+Jambon+Paris" target="_blank" rel="noopener">📍 12 rue du Jambon, Paris</a>
            <a href="tel:+33123456789">📞 01 23 45 67 89</a>
            <a href="mailto:contact@groindefolie.com">✉️ contact@groindefolie.com</a>
        </div>
    </div>
</footer>
</div>
</body>
</html>