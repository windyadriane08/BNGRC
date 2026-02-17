<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <div class="flex-between">
        <h2>Liste des Villes Sinistrées</h2>
        <a href="<?= BASE_URL ?>/villes/create" class="btn btn-primary">+ Nouvelle Ville</a>
    </div>
    
    <?php if (count($villes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Région</th>
                    <th>Date d'ajout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($villes as $ville): ?>
                    <tr>
                        <td><?= $ville['id_ville'] ?></td>
                        <td><strong><?= htmlspecialchars($ville['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($ville['region'] ?? '-') ?></td>
                        <td><?= date('d/m/Y', strtotime($ville['created_at'] ?? 'now')) ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>/villes/delete/<?= $ville['id_ville'] ?>" onsubmit="return confirm('Supprimer cette ville ?');" style="display:inline">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #7f8c8d; padding: 20px; text-align: center;">Aucune ville enregistrée. Ajoutez votre première ville!</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
