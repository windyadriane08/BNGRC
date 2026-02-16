<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card form-card">
    <h2>Saisie des Dons des Donneurs</h2>
    
    <form method="POST" action="/dons/store">
        <label for="type_id">Ressource *</label>
        <select id="type_id" name="type_id" required>
            <option value="">Sélectionner une ressource</option>
            <?php foreach($types as $type): ?>
                <option value="<?= $type['id_type'] ?>" data-cat="<?= $type['categorie'] ?>"><?= htmlspecialchars($type['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <div id="don-argent" style="display:none">
            <label for="montant">Montant (Ar) *</label>
            <input type="number" id="montant" name="montant" step="0.01" placeholder="Ex: 750000">
        </div>

        <div id="don-autres">
            <label for="quantite">Quantité *</label>
            <input type="number" id="quantite" name="quantite" step="0.01" placeholder="Ex: 200">
        </div>
        
        <button type="submit">Enregistrer le Don</button>
    </form>
    
    <p class="text-center mt-20">
        <a href="/dons">← Retour à la liste</a>
    </p>
</div>

<script>
const typeSelect = document.getElementById('type_id');
const blocArgent = document.getElementById('don-argent');
const blocAutres = document.getElementById('don-autres');
const montant = document.getElementById('montant');
const quantite = document.getElementById('quantite');

function toggleDonFields() {
    const opt = typeSelect.options[typeSelect.selectedIndex];
    const cat = opt ? opt.getAttribute('data-cat') : '';
    const isArgent = cat === 'argent';
    blocArgent.style.display = isArgent ? 'block' : 'none';
    blocAutres.style.display = isArgent ? 'none' : 'block';
    montant.required = isArgent;
    quantite.required = !isArgent;
}
typeSelect.addEventListener('change', toggleDonFields);
toggleDonFields();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
