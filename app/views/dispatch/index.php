<?php Flight::render('layouts/header', ['pageTitle' => 'Dispatch']); ?>

<?php
$unites = ['Riz' => 'kg', 'Eau potable' => 'L', 'Médicaments' => 'unité', 'Couvertures' => 'unité', 'Tentes' => 'unité', 'Vêtements' => 'pièce', 'Outils de construction' => 'unité', 'Argent' => 'Ar'];
function getUnite($r, $u) { return isset($u[$r]) ? ' ' . $u[$r] : ''; }
$reste = isset($_GET['reste']) ? (int)$_GET['reste'] : 0;
$mode_utilise = isset($_GET['mode']) ? (int)$_GET['mode'] : null;
$simulation = $simulation ?? null;
$modes = [1 => 'FIFO', 2 => 'Petits besoins', 3 => 'Proportionnel'];
?>

<div style="margin-bottom: 20px;">
    <h1 style="margin: 0 0 15px 0;">Dispatch</h1>
</div>

<?php if (count($attributions) > 0): ?>
<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
    <span style="color: #856404;"><strong>Veuillez réinitialiser</strong> avant d'effectuer un nouveau dispatch.</span>
    <form method="POST" action="<?= BASE_URL ?>/dispatch/reset" style="display: inline;">
        <button type="submit" class="btn btn-danger btn-sm">Réinitialiser</button>
    </form>
</div>
<?php else: ?>
<div style="margin-bottom: 20px;">
    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
        <strong>Simuler :</strong>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/simulate/1" style="display: inline;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Mode 1 (FIFO)</button>
        </form>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/simulate/2" style="display: inline;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Mode 2 (Petits)</button>
        </form>
        <form method="POST" action="<?= BASE_URL ?>/dispatch/simulate/3" style="display: inline;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Mode 3 (Prop.)</button>
        </form>
    </div>
    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
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
<?php endif; ?>

<?php if ($simulation): ?>
<div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <h3 style="margin: 0 0 10px 0;">
        Simulation Mode <?= $simulation['mode'] ?> (<?= htmlspecialchars($simulation['mode_nom'] ?? '') ?>)
    </h3>
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
                        <td>Don #<?= $attr['don_id'] ?></td>
                        <td><?= htmlspecialchars($attr['besoin_ville']) ?></td>
                        <td><?= number_format($attr['quantite'], 0, ',', ' ') ?><?= getUnite($attr['ressource'], $unites) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune attribution à simuler (données déjà dispatchées ou aucun besoin/don disponible)</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($mode_utilise && isset($modes[$mode_utilise])): ?>
<div style="background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
    <strong>Dispatch effectué :</strong> Mode <?= $mode_utilise ?> (<?= $modes[$mode_utilise] ?>)
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