<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="layout">
    <nav class="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <img src="/css/bngrc.png" alt="BNGRC" onerror="this.style.visibility='hidden'">
                <h1>BNGRC</h1>
            </div>
        </div>
        <a href="/">Accueil</a>
        <a href="/villes">Villes</a>
        <a href="/besoins">Besoins</a>
        <a href="/dons">Dons</a>
        <a href="/achats">Achats</a>
        <a href="/dispatch">Dispatch</a>
        <a href="/recap">Récap</a>
    </nav>

    <main class="content">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
