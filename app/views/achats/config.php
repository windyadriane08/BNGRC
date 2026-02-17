<?php Flight::render('layouts/header', ['pageTitle' => $pageTitle]); ?>

<div class="content-header">
    <h1>Configuration des achats</h1>
    <p class="text-muted">Paramètres du système d'achat</p>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <span>Frais d'achat</span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/achats/config">
            <div class="mb-3">
                <label for="frais_pct" class="form-label">Pourcentage de frais d'achat</label>
                <div class="input-group" style="max-width: 200px;">
                    <input type="number" class="form-control" id="frais_pct" name="frais_pct" 
                           value="<?php echo $fraisPct; ?>" min="0" max="100" step="0.1" required>
                    <span class="input-group-text">%</span>
                </div>
                <div class="form-text">
                    Ce pourcentage est ajouté au montant HT pour calculer le montant total des achats.
                    <br>Exemple: Pour un achat de 100 000 Ar avec <?php echo $fraisPct; ?>% de frais, 
                    le montant total sera <?php echo number_format(100000 * (1 + $fraisPct/100), 0, ',', ' '); ?> Ar.
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?= BASE_URL ?>/achats" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>
</div>

<?php Flight::render('layouts/footer'); ?>
