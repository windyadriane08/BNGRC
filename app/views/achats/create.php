<?php Flight::render('layouts/header', ['pageTitle' => $pageTitle]); ?>

<div class="content-header">
    <h1>Effectuer un achat</h1>
    <p class="text-muted">Acheter des ressources pour combler un besoin</p>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <span>Informations sur le besoin</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Ville:</strong><br>
                <?php echo htmlspecialchars($besoin['ville']); ?>
            </div>
            <div class="col-md-3">
                <strong>Ressource:</strong><br>
                <?php echo htmlspecialchars($besoin['ressource']); ?>
            </div>
            <div class="col-md-3">
                <strong>Quantité demandée:</strong><br>
                <?php echo $besoin['besoin_initial']; ?>
            </div>
            <div class="col-md-3">
                <strong>Quantité restante:</strong><br>
                <span class="text-danger fw-bold"><?php echo $quantiteRestante; ?></span>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <span>Budget disponible</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-value text-success"><?php echo number_format($argentDisponible, 0, ',', ' '); ?> Ar</div>
                    <div class="stat-label">Argent disponible</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $fraisPct; ?>%</div>
                    <div class="stat-label">Frais d'achat</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>Formulaire d'achat</span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/achats/store" id="achatForm">
            <input type="hidden" name="id_besoin" value="<?php echo $besoin['id_besoin']; ?>">
            
            <div class="mb-3">
                <label for="quantite_achetee" class="form-label">Quantité à acheter</label>
                <input type="number" class="form-control" id="quantite_achetee" name="quantite_achetee" 
                       min="1" max="<?php echo $quantiteRestante; ?>" value="<?php echo $quantiteRestante; ?>" required>
                <div class="form-text">Maximum: <?php echo $quantiteRestante; ?> (quantité restante à combler)</div>
            </div>
            
            <div class="mb-3">
                <label for="prix_unitaire" class="form-label">Prix unitaire (Ar)</label>
                <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" 
                       min="1" step="1" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Calcul du montant</label>
                <div class="p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Montant HT:</small><br>
                            <span id="montantHT">0</span> Ar
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Frais (<?php echo $fraisPct; ?>%):</small><br>
                            <span id="montantFrais">0</span> Ar
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">Montant total:</strong><br>
                            <strong class="text-primary" id="montantTotal">0</strong> Ar
                        </div>
                    </div>
                </div>
                <div id="budgetAlert" class="text-danger mt-2" style="display: none;">
                    Le montant dépasse le budget disponible !
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">Valider l'achat</button>
                <a href="<?= BASE_URL ?>/achats" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
const fraisPct = <?php echo $fraisPct; ?>;
const argentDisponible = <?php echo $argentDisponible; ?>;

function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(num));
}

function updateCalcul() {
    const quantite = parseInt(document.getElementById('quantite_achetee').value) || 0;
    const prixUnitaire = parseFloat(document.getElementById('prix_unitaire').value) || 0;
    
    const montantHT = quantite * prixUnitaire;
    const montantFrais = montantHT * (fraisPct / 100);
    const montantTotal = montantHT + montantFrais;
    
    document.getElementById('montantHT').textContent = formatNumber(montantHT);
    document.getElementById('montantFrais').textContent = formatNumber(montantFrais);
    document.getElementById('montantTotal').textContent = formatNumber(montantTotal);
    
    const budgetAlert = document.getElementById('budgetAlert');
    const submitBtn = document.getElementById('submitBtn');
    
    if (montantTotal > argentDisponible) {
        budgetAlert.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        budgetAlert.style.display = 'none';
        submitBtn.disabled = false;
    }
}

document.getElementById('quantite_achetee').addEventListener('input', updateCalcul);
document.getElementById('prix_unitaire').addEventListener('input', updateCalcul);

updateCalcul();
</script>

<?php Flight::render('layouts/footer'); ?>
