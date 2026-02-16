<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <div class="flex-between">
        <h2>Liste des Dons Reçus</h2>
        <a href="/dons/create" class="btn btn-primary">+ Nouveau Don</a>
    </div>
    
    <?php if (count($dons) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Ressource</th>
                    <th>Quantité</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dons as $don): ?>
                    <tr>
                        <td><?= $don['id_don'] ?></td>
                        <td><span class="badge badge-<?= $don['categorie'] ?>"><?= ucfirst($don['categorie']) ?></span></td>
                        <td><?= htmlspecialchars($don['ressource']) ?></td>
                        <td><strong><?= number_format($don['quantite'], 2) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #7f8c8d; padding: 20px; text-align: center;">Aucun don enregistré. Ajoutez votre premier don!</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
