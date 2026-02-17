<?php Flight::render('layouts/header', ['pageTitle' => 'Dispatch']); ?>

<?php
$unites = ['Riz' => 'kg', 'Eau potable' => 'L', 'Médicaments' => 'unité', 'Couvertures' => 'unité', 'Tentes' => 'unité', 'Vêtements' => 'pièce', 'Outils de construction' => 'unité', 'Argent' => 'Ar'];
function getUnite($r, $u) { return isset($u[$r]) ? ' ' . $u[$r] : ''; }
$reste = isset($_GET['reste']) ? (int)$_GET['reste'] : 0;
$simulation = $simulation ?? null;
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="margin: 0;">Dispatch</h1>
    <form method="POST" action="<?= BASE_URL ?>/dispatch/reset" style="display: inline;">
        <button type="submit" class="btn btn-danger">Réinitialiser</button>
    </form>
</div>

<div style="margin-bottom: 20px;">
    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
        <strong>Simuler :</strong>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/simulate/1" style="display: inline;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Mode 1</button>
        </form>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/simulate/2" style="display: inline;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Mode 2</button>
        </form>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/simulate/3" style="display: inline;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Mode 3</button>
        </form>
    </div>
    <div style="display: flex; gap: 10px;">
        <strong>Valider :</strong>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/validate/1" style="display: inline;">
            <button type="submit" class="btn btn-outline-success btn-sm">Mode 1</button>
        </form>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/validate/2" style="display: inline;">
            <button type="submit" class="btn btn-outline-success btn-sm">Mode 2</button>
        </form>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/validate/3" style="display: inline;">
            <button type="submit" class="btn btn-outline-success btn-sm">Mode 3</button>
        </form>
    </div>
</div>

<?php if ($simulation): ?>
<div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="margin: 0 0 10px 0;">Simulation <?= htmlspecialchars($simulation['mode_nom'] ?? 'Mode ' . $simulation['mode']) ?></h3>
    <?php if (!empty($simulation['types'])): ?>
        <?php foreach ($simulation['types'] as $type): ?>
            <?php if (!empty($type['attributions'])): ?>
            <p style="margin: 5px 0;"><strong><?= htmlspecialchars($type['type_nom']) ?></strong> (<?= count($type['attributions']) ?> attributions)</p>
            <table class="table" style="margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th>Don</th>
                        <th>Vers</th>
                        <th>Quantité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($type['attributions'] as $attr): ?>
                    <tr>
                        <td><?= htmlspecialchars($attr['don_ville'] ?? 'Don #' . $attr['don_id']) ?></td>
                        <td><?= htmlspecialchars($attr['besoin_ville']) ?></td>
                        <td><?= number_format($attr['quantite'], 0, ',', ' ') ?><?= getUnite($attr['ressource'], $unites) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (isset($type['reste']) && $type['reste'] > 0): ?>
            <p style="color: #856404;">Reste : <?= number_format($type['reste'], 0, ',', ' ') ?></p>
            <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune attribution à simuler</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($reste > 0): ?>
<p style="color: #856404;">Reste non distribué : <?= number_format($reste, 0, ',', ' ') ?></p>
<?php endif; ?>

<h3>Attributions effectuées (<?= count($attributions) ?>)</h3>
<?php if (count($attributions) > 0): ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ville</th>
            <th>Ressource</th>
            <th>Quantité</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($attributions as $attr): ?>
        <tr>
            <td><?= $attr['id_attribution'] ?></td>
            <td><?= htmlspecialchars($attr['ville']) ?></td>
            <td><?= htmlspecialchars($attr['ressource']) ?></td>
            <td><?= number_format($attr['quantite_attribuee'], 0, ',', ' ') ?><?= getUnite($attr['ressource'], $unites) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($attr['date_attribution'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="color: #666;">Aucun dispatch effectué</p>
<?php endif; ?>

<?php Flight::render('layouts/footer'); ?>
