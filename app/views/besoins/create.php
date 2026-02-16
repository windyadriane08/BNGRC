<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card form-card">
    <h2>Saisie des Besoins des Sinistrés</h2>
    
    <form method="POST" action="/besoins/store">
        <label for="ville_id">Ville *</label>
        <select id="ville_id" name="ville_id" required>
            <option value="">Sélectionner une ville</option>
            <?php foreach($villes as $ville): ?>
                <option value="<?= $ville['id_ville'] ?>"><?= htmlspecialchars($ville['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="type_id">Ressource *</label>
        <select id="type_id" name="type_id" required>
            <option value="">Sélectionner une ressource</option>
            <?php foreach($types as $type): ?>
                <option value="<?= $type['id_type'] ?>" data-cat="<?= $type['categorie'] ?>"><?= htmlspecialchars($type['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <div id="bloc-argent" style="display:none">
            <label for="montant">Montant (Ar) *</label>
            <input type="number" id="montant" name="montant" step="0.01" placeholder="Ex: 500000">
        </div>

        <div id="bloc-autres">
            <label for="quantite">Quantité *</label>
            <input type="number" id="quantite" name="quantite" step="0.01" placeholder="Ex: 100">
            
            <label for="prix_unitaire">Prix Unitaire (Ar) *</label>
            <input type="number" id="prix_unitaire" name="prix_unitaire" step="0.01" placeholder="Ex: 3000">
        </div>
        
        <button type="submit">Enregistrer le Besoin</button>
    </form>
    
    <p class="text-center mt-20">
        <a href="/besoins">← Retour à la liste</a>
    </p>
</div>

<script>
const typeSelect = document.getElementById('type_id');
const blocArgent = document.getElementById('bloc-argent');
const blocAutres = document.getElementById('bloc-autres');
const montant = document.getElementById('montant');
const quantite = document.getElementById('quantite');
const prix = document.getElementById('prix_unitaire');

function toggleFields() {
    const opt = typeSelect.options[typeSelect.selectedIndex];
    const cat = opt ? opt.getAttribute('data-cat') : '';
    const isArgent = cat === 'argent';
    blocArgent.style.display = isArgent ? 'block' : 'none';
    blocAutres.style.display = isArgent ? 'none' : 'block';
    montant.required = isArgent;
    quantite.required = !isArgent;
    prix.required = !isArgent;
}
typeSelect.addEventListener('change', toggleFields);
toggleFields();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
