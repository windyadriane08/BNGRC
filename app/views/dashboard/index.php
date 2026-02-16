<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1 class="text-center">Tableau de Bord </h1>

<div class="card">
    <h2>Statistiques Globales</h2>
    <div class="stats-grid">
        <div class="stat-box blue">
            <h3><?= $stats['nb_villes'] ?></h3>
            <p>Villes Sinistrées</p>
        </div>
        <div class="stat-box green">
            <h3><?= $stats['nb_besoins_total'] ?></h3>
            <p>Besoins Enregistrés</p>
        </div>
        <div class="stat-box orange">
            <h3><?= $stats['nb_dons_total'] ?></h3>
            <p>Dons Reçus</p>
        </div>
        <div class="stat-box purple">
            <h3><?= number_format($stats['pourcentage_couverture'], 1) ?>%</h3>
            <p>Taux de Couverture</p>
        </div>
    </div>
    
  
</div>

<div class="card">
    <h2>Vue par Ville</h2>
    <?php foreach($villes as $ville): ?>
        <h3 style="color: #34495e; margin-top: 20px;">🏙️ <?= htmlspecialchars($ville['nom']) ?> <?php if($ville['region']): ?>- <small><?= htmlspecialchars($ville['region']) ?></small><?php endif; ?></h3>
        
        <?php if (count($ville['besoins']) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Ressource</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Valeur Totale</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ville['besoins'] as $besoin): ?>
                        <tr>
                            <td><span class="badge badge-<?= $besoin['categorie'] ?>"><?= ucfirst($besoin['categorie']) ?></span></td>
                            <td><?= htmlspecialchars($besoin['ressource']) ?></td>
                            <td><?= number_format($besoin['quantite'], 2) ?></td>
                            <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                            <td><strong><?= number_format($besoin['valeur_totale'], 0, ',', ' ') ?> Ar</strong></td>
                            <td><?= date('d/m/Y', strtotime($besoin['date_saisie'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #7f8c8d; padding: 15px;">Aucun besoin enregistré pour cette ville.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Dons Reçus</h2>
    <?php if (count($dons) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Ressource</th>
                    <th>Quantité</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dons as $don): ?>
                    <tr>
                        <td><span class="badge badge-<?= $don['categorie'] ?>"><?= ucfirst($don['categorie']) ?></span></td>
                        <td><?= htmlspecialchars($don['ressource']) ?></td>
                        <td><?= number_format($don['quantite'], 2) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #7f8c8d; padding: 15px;">Aucun don enregistré.</p>
    <?php endif; ?>
</div>

<?php if (count($besoins_restants) > 0): ?>
<div class="card">
    <h2>Besoins Restants à Couvrir</h2>
    <table>
        <thead>
            <tr>
                <th>Ville</th>
                <th>Ressource</th>
                <th>Quantité Restante</th>
                <th>Prix Unitaire</th>
                <th>Valeur Restante</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($besoins_restants as $besoin): ?>
                <tr>
                    <td><?= htmlspecialchars($besoin['ville']) ?></td>
                    <td><?= htmlspecialchars($besoin['ressource']) ?></td>
                    <td><?= number_format($besoin['quantite_restante'], 2) ?></td>
                    <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                    <td><strong><?= number_format($besoin['valeur_restante'], 0, ',', ' ') ?> Ar</strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
