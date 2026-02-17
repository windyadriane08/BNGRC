<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="layout">
    <nav class="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <img src="<?= BASE_URL ?>/css/bngrc.png" alt="BNGRC" onerror="this.style.visibility='hidden'">
                <h1>BNGRC</h1>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/">Accueil</a>
        <a href="<?= BASE_URL ?>/villes">Villes</a>
        <a href="<?= BASE_URL ?>/besoins">Besoins</a>
        <a href="<?= BASE_URL ?>/dons">Dons</a>
        <a href="<?= BASE_URL ?>/achats">Achats</a>
        <a href="<?= BASE_URL ?>/dispatch">Dispatch</a>
        <a href="<?= BASE_URL ?>/recap">Récap</a>
    </nav>

    <main class="content">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
