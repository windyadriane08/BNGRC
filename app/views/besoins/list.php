<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <div class="flex-between">
        <h2>Liste des Besoins</h2>
        <a href="/besoins/create" class="btn btn-primary">+ Nouveau Besoin</a>
    </div>
    
    <?php if (count($besoins) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
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
                        <td><?= $besoin['id_besoin'] ?></td>
                        <td><?= htmlspecialchars($besoin['ville']) ?></td>
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
        <p style="color: #7f8c8d; padding: 20px; text-align: center;">Aucun besoin enregistré. Ajoutez votre premier besoin!</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
