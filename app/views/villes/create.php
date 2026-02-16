<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card form-card">
    <h2>Créer une Nouvelle Ville</h2>
    
    <form method="POST" action="/villes/store">
        <label for="nom">Nom de la Ville *</label>
        <input type="text" id="nom" name="nom" placeholder="Ex: Antananarivo" required>
        
        <label for="region">Région</label>
        <input type="text" id="region" name="region" placeholder="Ex: Analamanga">
        
        <button type="submit">Enregistrer la Ville</button>
    </form>
    
    <p class="text-center mt-20">
        <a href="/villes">← Retour à la liste</a>
    </p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
