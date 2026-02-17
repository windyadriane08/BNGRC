<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
// Mapping des unités par ressource
$unites = [
    'Riz' => 'kg',
    'Eau potable' => 'L',
    'Médicaments' => 'unité',
    'Couvertures' => 'unité',
    'Tentes' => 'unité',
    'Vêtements' => 'pièce',
    'Outils de construction' => 'unité',
    'Argent' => 'Ar'
];
function getUnite($ressource, $unites) {
    return isset($unites[$ressource]) ? ' ' . $unites[$ressource] : '';
}
?>

<div class="content-header">
    <h1>Tableau de Bord BNGRC</h1>
    <p class="text-muted">Vue d'ensemble de la gestion des secours</p>
</div>

<div class="card">
    <h2>📊 Statistiques Globales</h2>
    <div class="dashboard-grid">
        <a href="#section-villes" class="stat-box blue clickable" style="animation-delay: 0.1s;">
            <h3><?= $stats['nb_villes'] ?></h3>
            <p>🏙️ Villes Sinistrées</p>
        </a>
        <a href="#section-besoins" class="stat-box green clickable" style="animation-delay: 0.2s;">
            <h3><?= $stats['nb_besoins_total'] ?></h3>
            <p>📋 Besoins Enregistrés</p>
        </a>
        <a href="#section-dons" class="stat-box orange clickable" style="animation-delay: 0.3s;">
            <h3><?= $stats['nb_dons_total'] ?></h3>
            <p>🎁 Dons Reçus</p>
        </a>
        <a href="#section-restants" class="stat-box purple clickable" style="animation-delay: 0.4s;">
            <h3><?= number_format($stats['pourcentage_couverture'], 1) ?>%</h3>
            <p>📈 Taux de Couverture</p>
        </a>
    </div>
    
    <!-- Barre de progression globale -->
    <div style="margin-top: 25px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 600;">Progression globale</span>
            <span style="font-weight: 600; color: <?= $stats['pourcentage_couverture'] >= 100 ? 'var(--success)' : ($stats['pourcentage_couverture'] >= 50 ? 'var(--warning)' : 'var(--danger)') ?>;"><?= number_format($stats['pourcentage_couverture'], 1) ?>%</span>
        </div>
        <div class="progress" style="height: 30px;">
            <div class="progress-bar <?= $stats['pourcentage_couverture'] >= 100 ? 'bg-success' : ($stats['pourcentage_couverture'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                 style="width: <?= min(100, $stats['pourcentage_couverture']) ?>%; font-size: 14px;">
                <?= number_format($stats['pourcentage_couverture'], 1) ?>% couvert
            </div>
        </div>
    </div>
</div>

<div class="card" id="section-villes">
    <h2>🏙️ Vue par Ville</h2>
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
                            <td><?= number_format($besoin['quantite'], 0, ',', ' ') ?><?= getUnite($besoin['ressource'], $unites) ?></td>
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

<div class="card" id="section-dons">
    <h2>🎁 Dons Reçus</h2>
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
                        <td><?= number_format($don['quantite'], 0, ',', ' ') ?><?= getUnite($don['ressource'], $unites) ?></td>
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
<div class="card" id="section-restants">
    <h2>⚠️ Besoins Restants à Couvrir</h2>
    <table>
        <thead>
            <tr>
                <th>Ville</th>
                <th>Ressource</th>
                <th>Catégorie</th>
                <th>Quantité Restante</th>
                <th>Prix Unitaire</th>
                <th>Valeur Restante</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($besoins_restants as $besoin): ?>
                <tr>
                    <td><?= htmlspecialchars($besoin['ville']) ?></td>
                    <td><?= htmlspecialchars($besoin['ressource']) ?></td>
                    <td><span class="badge badge-<?= $besoin['categorie'] ?? 'nature' ?>"><?= ucfirst($besoin['categorie'] ?? 'nature') ?></span></td>
                    <td><?= number_format($besoin['quantite_restante'], 0, ',', ' ') ?><?= getUnite($besoin['ressource'], $unites) ?></td>
                    <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                    <td><strong><?= number_format($besoin['valeur_restante'], 0, ',', ' ') ?> Ar</strong></td>
                    <td>
                        <a href="<?= BASE_URL ?>/achats/create/<?= $besoin['id_besoin'] ?>" class="btn btn-sm btn-acheter">💰 Acheter</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="card" id="section-besoins">
    <h2>📋 Tous les Besoins Enregistrés</h2>
    <?php if (count($besoins) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Ville</th>
                    <th>Type</th>
                    <th>Ressource</th>
                    <th>Quantité</th>
                    <th>Prix Unit.</th>
                    <th>Valeur Totale</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($besoins as $besoin): ?>
                    <tr>
                        <td><?= htmlspecialchars($besoin['ville']) ?></td>
                        <td><span class="badge badge-<?= $besoin['categorie'] ?>"><?= ucfirst($besoin['categorie']) ?></span></td>
                        <td><?= htmlspecialchars($besoin['ressource']) ?></td>
                        <td><?= number_format($besoin['quantite'], 0, ',', ' ') ?><?= getUnite($besoin['ressource'], $unites) ?></td>
                        <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                        <td><strong><?= number_format($besoin['valeur_totale'], 0, ',', ' ') ?> Ar</strong></td>
                        <td><?= date('d/m/Y', strtotime($besoin['date_saisie'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #7f8c8d; padding: 15px;">Aucun besoin enregistré.</p>
    <?php endif; ?>
</div>

<script>
// Smooth scroll pour les liens internes
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            target.classList.add('highlight');
            setTimeout(() => target.classList.remove('highlight'), 2000);
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
