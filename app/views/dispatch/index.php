<?php Flight::render('layouts/header', ['pageTitle' => 'Dispatch']); ?>

<div class="content-header">
    <h1>Dispatch des dons</h1>
    <p class="text-muted">Répartition automatique des dons aux besoins (FIFO)</p>
</div>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Boutons Simuler / Valider -->
<div class="card mb-4">
    <div class="card-header">
        <span>Actions de dispatch</span>
    </div>
    <div class="card-body">
        <div class="d-flex gap-3 justify-content-center">
            <form method="POST" action="/dispatch/simulate" style="display: inline;">
                <button type="submit" class="btn btn-secondary btn-lg">
                    Simuler
                </button>
            </form>
            <form method="POST" action="/dispatch/validate" style="display: inline;" 
                  onsubmit="return confirm('Voulez-vous vraiment valider le dispatch ? Cette action créera les attributions en base.')">
                <button type="submit" class="btn btn-success btn-lg">
                    Valider
                </button>
            </form>
        </div>
        <p class="text-center text-muted mt-2">
            <small>Simuler permet de prévisualiser les attributions. Valider les enregistre définitivement.</small>
        </p>
    </div>
</div>

<!-- Résultat de la simulation -->
<?php if (isset($simulation) && $simulation): ?>
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <span>Résultat de la simulation</span>
        <span class="badge bg-light text-dark"><?php echo $simulation['total_attributions']; ?> attribution(s) prévue(s)</span>
    </div>
    <div class="card-body">
        <?php if ($simulation['total_attributions'] > 0): ?>
            <?php foreach ($simulation['types'] as $typeSimul): ?>
                <?php if (!empty($typeSimul['attributions'])): ?>
                    <h5><?php echo htmlspecialchars($typeSimul['type_nom']); ?></h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Don (origine)</th>
                                    <th>→</th>
                                    <th>Besoin (destination)</th>
                                    <th>Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($typeSimul['attributions'] as $attr): ?>
                                <tr>
                                    <td>Don #<?php echo $attr['don_id']; ?> (<?php echo htmlspecialchars($attr['don_ville']); ?>)</td>
                                    <td class="text-center">→</td>
                                    <td><?php echo htmlspecialchars($attr['besoin_ville']); ?></td>
                                    <td class="fw-bold"><?php echo number_format($attr['quantite'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="alert alert-info">
                Cliquez sur <strong>Valider</strong> pour appliquer ces attributions.
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Aucune attribution possible : pas de dons correspondant aux besoins restants.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Besoins restants -->
<div class="card mb-4">
    <div class="card-header">
        <span>Besoins restants à couvrir</span>
        <span class="badge bg-danger"><?php echo count($besoins_restants); ?></span>
    </div>
    <div class="card-body">
        <?php if (count($besoins_restants) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ville</th>
                            <th>Ressource</th>
                            <th>Quantité restante</th>
                            <th>Valeur (Ar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($besoins_restants as $besoin): ?>
                        <tr>
                            <td><?php echo $besoin['id_besoin']; ?></td>
                            <td><?php echo htmlspecialchars($besoin['ville']); ?></td>
                            <td><?php echo htmlspecialchars($besoin['ressource']); ?></td>
                            <td class="fw-bold"><?php echo number_format($besoin['quantite_restante'], 2); ?></td>
                            <td><?php echo number_format($besoin['valeur_restante'], 0, ',', ' '); ?> Ar</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-success text-center">Tous les besoins sont couverts !</p>
        <?php endif; ?>
    </div>
</div>

<!-- Dons disponibles -->
<div class="card mb-4">
    <div class="card-header">
        <span>Dons disponibles</span>
        <span class="badge bg-success"><?php echo count($dons_disponibles); ?></span>
    </div>
    <div class="card-body">
        <?php if (count($dons_disponibles) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ressource</th>
                            <th>Quantité disponible</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dons_disponibles as $don): ?>
                        <tr>
                            <td><?php echo $don['id_don']; ?></td>
                            <td><?php echo htmlspecialchars($don['nom'] ?? '-'); ?></td>
                            <td class="fw-bold"><?php echo number_format($don['quantite_restante'], 2); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($don['date_don'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Aucun don disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Historique des attributions -->
<div class="card">
    <div class="card-header">
        <span>Historique des attributions</span>
        <span class="badge bg-info"><?php echo count($attributions); ?></span>
    </div>
    <div class="card-body">
        <?php if (count($attributions) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ville</th>
                            <th>Ressource</th>
                            <th>Quantité attribuée</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($attributions as $attr): ?>
                        <tr>
                            <td><?php echo $attr['id_attribution']; ?></td>
                            <td><?php echo htmlspecialchars($attr['ville']); ?></td>
                            <td><?php echo htmlspecialchars($attr['ressource']); ?></td>
                            <td class="fw-bold"><?php echo number_format($attr['quantite_attribuee'], 2); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($attr['date_attribution'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Aucune attribution effectuée pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php Flight::render('layouts/footer'); ?>
