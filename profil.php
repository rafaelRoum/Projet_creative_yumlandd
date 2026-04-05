<?php
session_start();

$fichier_json = 'data/utilisateurs.json';
$utilisateurs = json_decode(file_get_contents('data/utilisateurs.json'), true);

$mon_id = $_SESSION['id']; 
$mon_profil = null;

foreach ($utilisateurs as $user) {
    if ($user['id'] == $mon_id) {
        $mon_profil = $user;
        break;
    }
}

if (isset($_POST['deco'])) { 
    session_destroy();   
    header("Location: index.php");
    exit();
}
$toutes_les_commandes = json_decode(file_get_contents('data/commandes.json'), true) ?? [];

$commandes = [];

foreach ($toutes_les_commandes as $cmd) {
    if ($cmd['id_client'] == $mon_id) {
        $commandes[] = $cmd;
    }
}

 
?>



<?php
$titre_page = "Profil - Le Groin de Folie";
include 'includes/header.php';
?>



<section class="place-cadre">
    <div class="cadre">
        <h2>Mon Profil</h2>

        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Nom</strong></td>
                    <td><?php echo strtoupper(htmlspecialchars($mon_profil['informations']['nom'])); ?></td>
                    <td><button>Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Prénom</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['informations']['prenom']); ?></td>
                    <td><button>Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['email']); ?></td>
                    <td><button>Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Naissance</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['informations']['naissance']); ?></td>
                    <td><button >Modifier</button></td>
                </tr>
                <tr>
                    <td><strong>Adresse</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['informations']['adresse']); ?></td>
                    <td><button >Modifier</button></td>
                </tr>

                <tr>
                    <td><strong>Rôle</strong></td>
                    <td><?php echo ucfirst(htmlspecialchars($mon_profil['role'])); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Statut</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['statut']); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Remise</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['niveau de remise']); ?> %</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Inscription</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['dates']['inscription']); ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><strong>Dernière Connexion</strong></td>
                    <td><?php echo htmlspecialchars($mon_profil['dates']['derniere_connexion']); ?></td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>

        <form method="POST" style="margin-top: 30px; text-align: center;">
            <button type="submit" name="deco" class="btn-deco">Se déconnecter</button>
        </form>
    </div>
</section>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2 id="commandes"class="france-ancien-livre">Historique des commandes</h2>
        
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th>N°</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Livreur/Récuperer</th>
                    <th>Détails</th>
                    <th>Noter</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): ?>
                <tr>

                    <td><strong><?php echo $cmd['id_commande']; ?></strong></td>

                    <td><?php echo $cmd['date_heure']?></td>
                    
                    <td>
                        <?php if($cmd['statut'] === "en préparation"): ?>
                            <p style="color:#2196F3">En préparation</p>
                        <?php endif; ?>
                        <?php if($cmd['statut'] === "prêt"): ?>
                            <p style="color:#ff9102">Prêt</p>
                        <?php endif; ?>
                        <?php if($cmd['statut'] === "en livraison"): ?>
                            <p style="color:#ff9102">En livraison</p>
                        <?php endif; ?>
                        <?php if($cmd['statut'] === "terminée"): ?>
                            <p style="color:#08a021">Terminée</p>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($cmd['type_livraison'] === 'livraison'): ?> 
                            <?php if($cmd['statut'] === 'prêt'): ?>
                                <p>Non attribué</p>
                            <?php endif; ?>
                            <?php if($cmd['statut'] === 'en préparation'): ?>
                                <p>Non attribué</p>
                            <?php endif; ?>
                            <?php if($cmd['statut'] === 'en livraison' || $cmd['statut'] === 'terminée' ): ?>
                                <?php echo ($cmd['livreur']); ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($cmd['type_livraison'] === 'sur place'): ?>
                            <?php if($cmd['statut'] === 'prêt'): ?>
                                <p>A récuperer</p>
                            <?php endif; ?>
                            <?php if($cmd['statut'] === 'en préparation' || $cmd['statut'] === 'terminée'): ?>
                                <p>-</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>

                    <td style="text-align: center;"> 
                        <a href="#detail-<?php echo $cmd['id_commande']; ?>" class="voir-profil-btn" >Détails</a>
                        <div id="detail-<?php echo $cmd['id_commande']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                <h3>Commande <?php echo $cmd['id_commande']; ?></h3>
                                <p>Type : <?php echo $cmd['type_livraison']; ?> | Date : <?php echo $cmd['date_heure']; ?></p>
                                <hr>
                                
                                <div style="margin: 15px">
                                    <h4 style="margin-bottom: 15px;">Articles à préparer :</h4>
                                    <?php foreach ($cmd['contenu'] as $item): ?>
                                        <strong><?php echo ($item['nom']); ?></strong>
                                        <p>Note : <?php echo implode(', ', $item['options_choisies']); ?></p>
                                    <?php endforeach; ?>
                                </div>

                                <div class="paiement">
                                    <p><strong>Total :</strong> <?php echo number_format($cmd['paiement']['montant_total'], 2); ?> €</p>
                                    <p><strong>Paiement :</strong> <?php echo strtoupper($cmd['paiement']['methode']); ?> <span style="color:#08a021"> <?php echo $cmd['paiement']['statut']; ?> </span> </p>
                                    <?php if(!empty($cmd['adresse'])): ?>
                                        <p><strong>Adresse :</strong> <?php echo ($cmd['adresse']); ?></p>
                                    <?php endif; ?>
                                </div>
                                    <a href="#!" class="btn-lien-paiement">Fermer</a> 
                            </div>
                        </div>
                    </td>

                    <td>
                        <a href="notation.php" class="voir-profil-btn">Noter</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
include 'includes/footer.php';
?>