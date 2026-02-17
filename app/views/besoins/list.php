<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$unites = ['Riz' => 'kg', 'Eau potable' => 'L', 'Médicaments' => 'unité', 'Couvertures' => 'unité', 'Tentes' => 'unité', 'Vêtements' => 'pièce', 'Outils de construction' => 'unité', 'Argent' => 'Ar'];
function getUnite($r, $u) { return isset($u[$r]) ? ' ' . $u[$r] : ''; }

// Grouper par catégorie (materiaux, nature, argent)
$besoinsParType = [];
$ordreTypes = ['materiaux' => 'Matériel', 'nature' => 'Nature', 'argent' => 'Argent'];
foreach ($besoins as $besoin) {
    $type = $besoin['categorie'] ?? 'autre';
    if (!isset($besoinsParType[$type])) {
        $besoinsParType[$type] = [];
    }
    $besoinsParType[$type][] = $besoin;
}
?>

<div class="card">
    <div class="flex-between">
        <h2>Liste des Besoins</h2>
        <a href="<?= BASE_URL ?>/besoins/create" class="btn btn-primary">+ Nouveau Besoin</a>
    </div>
    
    <?php if (count($besoins) > 0): ?>
        <?php foreach ($ordreTypes as $typeKey => $typeLabel): ?>
        <?php if (isset($besoinsParType[$typeKey])): ?>
        <h4 style="margin: 20px 0 10px 0; padding: 8px; background: #f5f5f5; border-radius: 4px;">
            <?= $typeLabel ?>
            <span style="font-weight: normal; color: #666;">(<?= count($besoinsParType[$typeKey]) ?>)</span>
        </h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ville</th>
                    <th>Ressource</th>
                    <th>Quantité</th>
                    <th>Prix Unit.</th>
                    <th>Valeur Totale</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($besoinsParType[$typeKey] as $besoin): ?>
                    <tr>
                        <td><?= $besoin['id_besoin'] ?></td>
                        <td><?= htmlspecialchars($besoin['ville']) ?></td>
                        <td><?= htmlspecialchars($besoin['ressource']) ?></td>
                        <td><?= number_format($besoin['quantite'], 0, ',', ' ') ?><?= getUnite($besoin['ressource'], $unites) ?></td>
                        <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                        <td><strong><?= number_format($besoin['valeur_totale'], 0, ',', ' ') ?> Ar</strong></td>
                        <td><?= date('d/m/Y', strtotime($besoin['date_saisie'])) ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>/besoins/delete/<?= $besoin['id_besoin'] ?>" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
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
        <p style="color: #7f8c8d; padding: 20px; text-align: center;">Aucun besoin enregistré. Ajoutez votre premier besoin!</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
