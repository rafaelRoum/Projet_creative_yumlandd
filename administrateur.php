<?php
session_start();

$fichier_json = 'data/utilisateurs.json';
$utilisateurs = [];

if (file_exists($fichier_json)) {
    $json_data = file_get_contents($fichier_json);
    $utilisateurs = json_decode($json_data, true) ?? [];
}
?>

<?php
$titre_page = "Administrateur - Le Groin de Folie";
include 'includes/header.php';
?>

<main class="admin-cadre-placement">
    <div class="admin-cadre">
        <h2>Gestion des Utilisateurs</h2>            
        <table class="tab-utilisateur" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Remise</th>
                    <th>Droit</th>
                    <th>detail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td>
                        <strong><?php echo ($user['informations']['nom']); ?></strong> 
                        <?php echo ($user['informations']['prenom']); ?>
                    </td>

                    <td>
                        <?php echo ($user['email']); ?>
                    </td>

                    <td>
                        <span class="role-badge role-<?php echo ($user['role']); ?>">
                            <?php echo ($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if($user['statut'] === "Standard"): ?>
                            <select>
                                <option>Standard</option>
                                <option>Premium</option>
                                <option>VIP</option>
                            </select>
                        <?php endif; ?>
                        <?php if($user['statut'] === "Premium"): ?>
                            <select>
                                <option>Premium</option>
                                <option>Standard</option>
                                <option>VIP</option>
                            </select>
                        <?php endif; ?>
                        <?php if($user['statut'] === "VIP"): ?>
                            <select>
                                <option>VIP</option>
                                <option>Standard</option>
                                <option>Premium</option>
                            </select>
                        <?php endif; ?>
                    </td>

                    <td class="admin-actions">
                        <input type="number" value="<?php echo $user['niveau de remise']?>" class="input-remise" min="O" max="50" oninput="if(this.value < 0) this.value = 0; if(this.value > 50) this.value = 50;"> <p>%</p>                     
                    </td>
                    <td>
                        <?php if($user['droit'] === "normal"): ?>
                            <select>
                                <option>normal</option>
                                <option>bloquer</option>
                                <option>désactiver</option>
                            </select>
                        <?php endif; ?>
                        <?php if($user['droit'] === "bloquer"): ?>
                            <select>
                                <option>bloquer</option>
                                <option>débloquer</option>
                                <option>désactiver</option>
                            </select>
                        <?php endif; ?>
                        <?php if($user['droit'] === "desactiver"): ?>
                            <select>
                                <option>désactiver</option>
                                <option>bloquer</option>
                                <option>débloquer</option>
                            </select>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="#user-<?php echo $user['id']; ?>" class="voir-profil-btn">Détails</a>
                        <div id="user-<?php echo $user['id']; ?>" class="modal-fond">
                            <div class="modal-contenu">
                                
                                <h2>Fiche de <?php echo ($user['informations']['prenom']); ?></h2>
                                <hr>
                                <div class="infos-details" style="margin-bottom: 10px">
                                    <p><strong>Identifiant :</strong> <?php echo ($user['id']); ?></p>                                    
                                    <p><strong>Nom :</strong> <?php echo strtoupper($user['informations']['nom']); ?></p>                                    
                                    <p><strong>Prénom :</strong> <?php echo ($user['informations']['prenom']); ?></p>                     
                                    <p><strong>Email :</strong> <?php echo ($user['email']); ?></p>                                    
                                    <p><strong>Date de naissance :</strong> <?php echo ($user['informations']['naissance']); ?></p>                                   
                                    <p><strong>Adresse :</strong> <?php echo ($user['informations']['adresse']); ?></p>   
                                    <p><strong>Rôle :</strong> <?php echo ($user['role']); ?></p>
                                    <p><strong>Statut :</strong> <?php echo ($user['statut']); ?></p>
                                    <p><strong>Niveau de remise :</strong> <?php echo ($user['niveau de remise']); ?> %</p>
                                    <p><strong>Droit :</strong> <?php echo ($user['droit']); ?></p>
                                    <p><strong>Date d'inscription :</strong> <?php echo ($user['dates']['inscription']); ?></p>
                                    <p><strong>Dernière connexion :</strong> <?php echo ($user['dates']['derniere_connexion']); ?></p>
                                </div>
                                    <a href="#!" class="btn-lien-paiement">Fermer</a> 
                            </div>
                        </div>
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