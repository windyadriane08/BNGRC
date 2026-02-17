<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$unites = ['Riz' => 'kg', 'Eau potable' => 'L', 'Médicaments' => 'unité', 'Couvertures' => 'unité', 'Tentes' => 'unité', 'Vêtements' => 'pièce', 'Outils de construction' => 'unité', 'Argent' => 'Ar'];
function getUnite($r, $u) { return isset($u[$r]) ? ' ' . $u[$r] : ''; }

// Grouper par catégorie (materiaux, nature, argent)
$donsParType = [];
$ordreTypes = ['materiaux' => 'Matériel', 'nature' => 'Nature', 'argent' => 'Argent'];
foreach ($dons as $don) {
    $type = $don['categorie'] ?? 'autre';
    if (!isset($donsParType[$type])) {
        $donsParType[$type] = [];
    }
    $donsParType[$type][] = $don;
}
?>

<div class="card">
    <div class="flex-between">
        <h2>Liste des Dons Reçus</h2>
        <a href="<?= BASE_URL ?>/dons/create" class="btn btn-primary">+ Nouveau Don</a>
    </div>
    
    <?php if (count($dons) > 0): ?>
        <?php foreach ($ordreTypes as $typeKey => $typeLabel): ?>
        <?php if (isset($donsParType[$typeKey])): ?>
        <h4 style="margin: 20px 0 10px 0; padding: 8px; background: #f5f5f5; border-radius: 4px;">
            <?= $typeLabel ?>
            <span style="font-weight: normal; color: #666;">(<?= count($donsParType[$typeKey]) ?>)</span>
        </h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ressource</th>
                    <th>Quantité</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($donsParType[$typeKey] as $don): ?>
                    <tr>
                        <td><?= $don['id_don'] ?></td>
                        <td><?= htmlspecialchars($don['ressource']) ?></td>
                        <td><strong><?= number_format($don['quantite'], 0, ',', ' ') ?><?= getUnite($don['ressource'], $unites) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>/dons/delete/<?= $don['id_don'] ?>" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
                                <button type="submit" class="btn btn-sm btn-danger">❌</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #7f8c8d; padding: 20px; text-align: center;">Aucun don enregistré. Ajoutez votre premier don!</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
