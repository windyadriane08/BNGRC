<?php Flight::render('layouts/header', ['pageTitle' => $pageTitle]); ?>

<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Récapitulatif</h1>
            <p class="text-muted">Vue d'ensemble des besoins et des dons</p>
        </div>
        <button type="button" class="btn btn-primary" id="btnActualiser">
            <span id="refreshIcon">↻</span> Actualiser
        </button>
    </div>
</div>

<div id="recapContent">
    <!-- Totaux globaux en montants -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value" id="totalBesoins"><?php echo number_format($recap['totaux']['besoins'], 0, ',', ' '); ?> Ar</div>
                <div class="stat-label">Besoins totaux (montant)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-success" id="totalSatisfaits"><?php echo number_format($recap['totaux']['satisfaits'], 0, ',', ' '); ?> Ar</div>
                <div class="stat-label">Satisfaits (montant)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-danger" id="totalRestants"><?php echo number_format($recap['totaux']['restants'], 0, ',', ' '); ?> Ar</div>
                <div class="stat-label">Restants (montant)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-primary" id="pourcentageCouverture"><?php echo $recap['totaux']['pourcentage_couverture']; ?>%</div>
                <div class="stat-label">Couverture</div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value" id="totalDons"><?php echo $recap['totaux']['dons']; ?></div>
                <div class="stat-label">Total dons reçus (quantité)</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value text-success" id="totalAttribues"><?php echo $recap['totaux']['attribues']; ?></div>
                <div class="stat-label">Dons attribués (quantité)</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value text-info" id="argentDisponible"><?php echo number_format($recap['totaux']['argent_disponible'], 0, ',', ' '); ?> Ar</div>
                <div class="stat-label">Argent disponible</div>
            </div>
        </div>
    </div>
    
    <!-- Détail par ville -->
    <div class="card">
        <div class="card-header">
            <span>Détail par ville (en montants)</span>
            <small class="text-muted" id="lastUpdate">Dernière mise à jour: <?php echo $recap['timestamp']; ?></small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ville</th>
                            <th class="text-center">Besoins (Ar)</th>
                            <th class="text-center">Satisfaits (Ar)</th>
                            <th class="text-center">Restants (Ar)</th>
                            <th class="text-center">Couverture</th>
                            <th class="text-center">Achats</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($recap['par_ville'] as $ville): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($ville['nom_ville']); ?></td>
                            <td class="text-center"><?php echo number_format($ville['total_besoins'], 0, ',', ' '); ?></td>
                            <td class="text-center text-success"><?php echo number_format($ville['total_satisfaits'], 0, ',', ' '); ?></td>
                            <td class="text-center text-danger"><?php echo number_format($ville['total_restants'], 0, ',', ' '); ?></td>
                            <td class="text-center">
                                <div class="progress" style="min-width: 80px;">
                                    <div class="progress-bar <?php echo $ville['pourcentage_couverture'] >= 100 ? 'bg-success' : ($ville['pourcentage_couverture'] >= 50 ? 'bg-warning' : 'bg-danger'); ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo min(100, $ville['pourcentage_couverture']); ?>%"
                                         aria-valuenow="<?php echo $ville['pourcentage_couverture']; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo $ville['pourcentage_couverture']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><?php echo number_format($ville['total_achats'], 0, ',', ' '); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(num));
}

function getProgressBarClass(pct) {
    if (pct >= 100) return 'bg-success';
    if (pct >= 50) return 'bg-warning';
    return 'bg-danger';
}

document.getElementById('btnActualiser').addEventListener('click', function() {
    const btn = this;
    const icon = document.getElementById('refreshIcon');
    
    btn.disabled = true;
    icon.style.animation = 'spin 1s linear infinite';
    
    fetch('<?= BASE_URL ?>/recap/data')
        .then(response => response.json())
        .then(data => {
            // Mise à jour des totaux (en montants Ar)
            document.getElementById('totalBesoins').textContent = formatNumber(data.totaux.besoins) + ' Ar';
            document.getElementById('totalSatisfaits').textContent = formatNumber(data.totaux.satisfaits) + ' Ar';
            document.getElementById('totalRestants').textContent = formatNumber(data.totaux.restants) + ' Ar';
            document.getElementById('pourcentageCouverture').textContent = data.totaux.pourcentage_couverture + '%';
            document.getElementById('totalDons').textContent = data.totaux.dons;
            document.getElementById('totalAttribues').textContent = data.totaux.attribues;
            document.getElementById('argentDisponible').textContent = formatNumber(data.totaux.argent_disponible) + ' Ar';
            document.getElementById('lastUpdate').textContent = 'Dernière mise à jour: ' + data.timestamp;
            
            // Mise à jour du tableau
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            
            data.par_ville.forEach(ville => {
                const tr = document.createElement('tr');
                const progressClass = getProgressBarClass(ville.pourcentage_couverture);
                const progressWidth = Math.min(100, ville.pourcentage_couverture);
                
                tr.innerHTML = `
                    <td class="fw-bold">${escapeHtml(ville.nom_ville)}</td>
                    <td class="text-center">${formatNumber(ville.total_besoins)}</td>
                    <td class="text-center text-success">${formatNumber(ville.total_satisfaits)}</td>
                    <td class="text-center text-danger">${formatNumber(ville.total_restants)}</td>
                    <td class="text-center">
                        <div class="progress" style="min-width: 80px;">
                            <div class="progress-bar ${progressClass}" 
                                 role="progressbar" 
                                 style="width: ${progressWidth}%"
                                 aria-valuenow="${ville.pourcentage_couverture}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${ville.pourcentage_couverture}%
                            </div>
                        </div>
                    </td>
                    <td class="text-center">${formatNumber(ville.total_achats)}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erreur lors de l\'actualisation:', error);
            alert('Erreur lors de l\'actualisation des données');
        })
        .finally(() => {
            btn.disabled = false;
            icon.style.animation = '';
        });
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#refreshIcon {
    display: inline-block;
}

.progress {
    height: 20px;
}

.progress-bar {
    font-size: 0.75rem;
    line-height: 20px;
}
</style>

<?php Flight::render('layouts/footer'); ?>
