<div class="content-header">
    <h2><i class="fas fa-percentage"></i> Gestion des Taux d'Intérêt</h2>
    <button class="btn btn-primary" onclick="ouvrirModalTaux()">
        <i class="fas fa-plus"></i> Nouveau Taux
    </button>
</div>

<div class="content-body">
    <!-- Section des filtres -->
    <div class="filters-section">
        <div class="filters-row">
            <div class="filter-group">
                <label for="filterTypeClient">Type de client :</label>
                <select id="filterTypeClient" onchange="filtrerTaux()">
                    <option value="">Tous les types</option>
                    <?php foreach ($typesClient as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterTypePret">Type de prêt :</label>
                <select id="filterTypePret" onchange="filtrerTaux()">
                    <option value="">Tous les types</option>
                    <?php foreach ($typesPret as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-actions">
                <button class="btn btn-secondary" onclick="reinitialiserFiltres()">
                    <i class="fas fa-undo"></i> Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau des taux -->
    <div class="table-container">
        <table class="table" id="tableTaux">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <th><i class="fas fa-users"></i> Type Client</th>
                    <th><i class="fas fa-university"></i> Type Prêt</th>
                    <th><i class="fas fa-percentage"></i> Taux (%)</th>
                    <th><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody id="corpsTableauTaux">
                <!-- Les données seront chargées via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- État vide -->
    <div id="etatVide" class="empty-state" style="display: none;">
        <i class="fas fa-percentage fa-3x"></i>
        <h3>Aucun taux configuré</h3>
        <p>Commencez par ajouter des taux d'intérêt pour vos différents types de clients et de prêts.</p>
        <button class="btn btn-primary" onclick="ouvrirModalTaux()">
            <i class="fas fa-plus"></i> Ajouter un taux
        </button>
    </div>

    <!-- Loading overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Chargement...</p>
        </div>
    </div>
</div>

<!-- Modal pour ajouter/modifier un taux -->
<div id="modalTaux" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="titreModa">Ajouter un Taux</h3>
            <span class="close" onclick="fermerModalTaux()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="formTaux">
                <input type="hidden" id="tauxId" name="id">
                
                <div class="form-group">
                    <label for="typeClientSelect">Type de Client *</label>
                    <select id="typeClientSelect" name="type_client_id" required>
                        <option value="">Sélectionnez un type de client</option>
                        <?php foreach ($typesClient as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="typePretSelect">Type de Prêt *</label>
                    <select id="typePretSelect" name="type_pret_id" required>
                        <option value="">Sélectionnez un type de prêt</option>
                        <?php foreach ($typesPret as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tauxInteret">Taux d'Intérêt (%) *</label>
                    <input type="number" id="tauxInteret" name="taux_interet" 
                           step="0.01" min="0" max="100" required 
                           placeholder="Ex: 5.50">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="fermerModalTaux()">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Styles pour la page gestion des taux */
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.content-header h2 {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.content-body {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Section des filtres */
.filters-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.filters-row {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.filter-group select {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    background: white;
    transition: border-color 0.2s ease;
}

.filter-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

/* Boutons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background-color: #2563eb;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.btn-warning {
    background-color: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
}

.btn-danger {
    background-color: #ef4444;
    color: white;
}

.btn-danger:hover {
    background-color: #dc2626;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* État vide */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

/* Tableau */
.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.table th,
.table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.table th {
    background-color: #f9fafb;
    font-weight: 600;
    color: #374151;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr:hover {
    background-color: #f9fafb;
}

/* Actions */
.actions {
    display: flex;
    gap: 0.5rem;
}

.actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.actions .btn i {
    margin-right: 0.25rem;
}

/* Loading overlay */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.loading-spinner {
    text-align: center;
    color: #6b7280;
}

.loading-spinner i {
    color: #3b82f6;
    margin-bottom: 0.5rem;
}

.loading-spinner p {
    font-size: 0.875rem;
    margin: 0;
}

/* Modal */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.close {
    font-size: 1.5rem;
    font-weight: bold;
    color: #9ca3af;
    cursor: pointer;
    padding: 0.25rem;
    line-height: 1;
}

.close:hover {
    color: #374151;
}

.modal-body {
    padding: 1.5rem;
}

/* Formulaires */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .content-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .filters-row {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .table {
        font-size: 0.75rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
}

/* Styles spécifiques */
.taux-value {
    font-weight: 600;
    color: #059669;
}

.taux-value::after {
    content: '%';
    font-weight: normal;
    color: #6b7280;
}
</style>

<script>
const apiBase = "<?= BASE_URL ?>";
let tousLesTaux = [];
let modeModification = false;

// Charger les données au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    chargerTaux();
});

function chargerTaux() {
    afficherChargement(true);
    
    fetch(`${apiBase}/admin/taux/ajax`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tousLesTaux = data.data;
                afficherTaux(tousLesTaux);
            } else {
                console.error('Erreur:', data.message);
                afficherEtatVide();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherEtatVide();
        })
        .finally(() => {
            afficherChargement(false);
        });
}

function afficherTaux(taux) {
    const tbody = document.getElementById('corpsTableauTaux');
    const etatVide = document.getElementById('etatVide');
    
    if (taux.length === 0) {
        tbody.innerHTML = '';
        etatVide.style.display = 'block';
        document.querySelector('.table-container').style.display = 'none';
        return;
    }
    
    etatVide.style.display = 'none';
    document.querySelector('.table-container').style.display = 'block';
    
    tbody.innerHTML = taux.map(taux => `
        <tr>
            <td>${taux.id}</td>
            <td>${taux.type_client_nom || 'N/A'}</td>
            <td>${taux.type_pret_nom || 'N/A'}</td>
            <td><span class="taux-value">${parseFloat(taux.taux_interet).toFixed(2)}</span></td>
            <td>
                <div class="actions">
                    <button class="btn btn-warning btn-sm" onclick="modifierTaux(${taux.id})">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="supprimerTaux(${taux.id})">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function afficherEtatVide() {
    document.getElementById('corpsTableauTaux').innerHTML = '';
    document.getElementById('etatVide').style.display = 'block';
    document.querySelector('.table-container').style.display = 'none';
}

function afficherChargement(afficher) {
    document.getElementById('loadingOverlay').style.display = afficher ? 'block' : 'none';
}

function filtrerTaux() {
    const typeClientFiltre = document.getElementById('filterTypeClient').value;
    const typePretFiltre = document.getElementById('filterTypePret').value;
    
    let tauxFiltres = tousLesTaux;
    
    if (typeClientFiltre) {
        tauxFiltres = tauxFiltres.filter(taux => taux.type_client_id == typeClientFiltre);
    }
    
    if (typePretFiltre) {
        tauxFiltres = tauxFiltres.filter(taux => taux.type_pret_id == typePretFiltre);
    }
    
    afficherTaux(tauxFiltres);
}

function reinitialiserFiltres() {
    document.getElementById('filterTypeClient').value = '';
    document.getElementById('filterTypePret').value = '';
    afficherTaux(tousLesTaux);
}

function ouvrirModalTaux() {
    modeModification = false;
    document.getElementById('titreModa').textContent = 'Ajouter un Taux';
    document.getElementById('formTaux').reset();
    document.getElementById('tauxId').value = '';
    document.getElementById('modalTaux').style.display = 'flex';
}

function fermerModalTaux() {
    document.getElementById('modalTaux').style.display = 'none';
    document.getElementById('formTaux').reset();
}

function modifierTaux(id) {
    const taux = tousLesTaux.find(t => t.id == id);
    if (!taux) return;
    
    modeModification = true;
    document.getElementById('titreModa').textContent = 'Modifier le Taux';
    document.getElementById('tauxId').value = taux.id;
    document.getElementById('typeClientSelect').value = taux.type_client_id;
    document.getElementById('typePretSelect').value = taux.type_pret_id;
    document.getElementById('tauxInteret').value = taux.taux_interet;
    document.getElementById('modalTaux').style.display = 'flex';
}

function supprimerTaux(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce taux ?')) {
        return;
    }
    
    fetch(`${apiBase}/admin/taux/supprimer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            chargerTaux();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression');
    });
}

// Gestion du formulaire
document.getElementById('formTaux').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Validation
    if (!data.type_client_id || !data.type_pret_id || !data.taux_interet) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }
    
    if (parseFloat(data.taux_interet) < 0 || parseFloat(data.taux_interet) > 100) {
        alert('Le taux doit être entre 0 et 100%');
        return;
    }
    
    const url = modeModification ? `${apiBase}/admin/taux/modifier` : `${apiBase}/admin/taux/inserer`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fermerModalTaux();
            chargerTaux();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'enregistrement');
    });
});

// Fermer le modal en cliquant à l'extérieur
document.getElementById('modalTaux').addEventListener('click', function(e) {
    if (e.target === this) {
        fermerModalTaux();
    }
});
</script>
