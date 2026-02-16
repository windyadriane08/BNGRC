<?php Flight::render('layouts/header', ['pageTitle' => $pageTitle]); ?>

<div class="content-header">
    <h1>Liste des Achats</h1>
    <p class="text-muted">Achats effectués avec les dons en argent</p>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <span>Argent disponible</span>
        <a href="/achats/config" class="btn btn-sm btn-secondary">Configurer frais</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value text-success"><?php echo number_format($argentDisponible, 0, ',', ' '); ?> Ar</div>
                    <div class="stat-label">Argent disponible pour achats</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value text-primary"><?php echo number_format($totalAchats, 0, ',', ' '); ?> Ar</div>
                    <div class="stat-label">Total des achats effectués</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $fraisPct; ?>%</div>
                    <div class="stat-label">Frais d'achat configurés</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($besoinsRestants)): ?>
<div class="card mb-4">
    <div class="card-header">
        <span>Besoins restants à combler</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Ressource</th>
                        <th>Quantité restante</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($besoinsRestants as $besoin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($besoin['ville']); ?></td>
                        <td><?php echo htmlspecialchars($besoin['ressource']); ?></td>
                        <td><?php echo $besoin['quantite_restante']; ?></td>
                        <td>
                            <a href="/achats/create/<?php echo $besoin['id_besoin']; ?>" class="btn btn-sm btn-primary">
                                Acheter
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <span>Historique des achats</span>
    </div>
    <div class="card-body">
        <?php if (empty($achats)): ?>
            <p class="text-muted text-center">Aucun achat effectué pour le moment</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Ville</th>
                            <th>Ressource</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Frais (%)</th>
                            <th>Montant total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achats as $achat): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($achat['date_achat'])); ?></td>
                            <td><?php echo htmlspecialchars($achat['nom_ville'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($achat['nom_type'] ?? '-'); ?></td>
                            <td><?php echo $achat['quantite_achetee']; ?></td>
                            <td><?php echo number_format($achat['prix_unitaire'], 0, ',', ' '); ?> Ar</td>
                            <td><?php echo $achat['frais_pct']; ?>%</td>
                            <td class="fw-bold"><?php echo number_format($achat['montant_total'], 0, ',', ' '); ?> Ar</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php Flight::render('layouts/footer'); ?>
