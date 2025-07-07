<div class="content-header">
    <h2>Liste des Prêts</h2>
    <button class="btn btn-primary" onclick="ouvrirModalPret()">
        <i class="fas fa-plus"></i> Nouveau Prêt
    </button>
</div>

<div class="content-body">
    <?php if (empty($prets)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h3>Aucun prêt trouvé</h3>
            <p>Il n'y a actuellement aucun prêt enregistré dans le système.</p>
            <button class="btn btn-primary" onclick="ouvrirModalPret()">Créer le premier prêt</button>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Type de Prêt</th>
                        <th>Date de Demande</th>
                        <th>Durée (mois)</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prets as $pret): ?>
                        <?php
                        // Trouver le nom du client correspondant
                        $nomClient = 'Client non trouvé';
                        foreach ($clients as $client) {
                            if ($client['id'] == $pret['client_id']) {
                                $nomClient = $client['nom']; // Suppression de prenom qui n'existe pas
                                break;
                            }
                        }
                        
                        // Déterminer le statut
                        $statutText = '';
                        $statutClass = '';
                        switch ($pret['id_statut']) {
                            case 1:
                                $statutText = 'En attente';
                                $statutClass = 'status-pending';
                                break;
                            case 2:
                                $statutText = 'Approuvé';
                                $statutClass = 'status-approved';
                                break;
                            case 3:
                                $statutText = 'Rejeté';
                                $statutClass = 'status-rejected';
                                break;
                            case 4:
                                $statutText = 'En cours';
                                $statutClass = 'status-ongoing';
                                break;
                            case 5:
                                $statutText = 'Terminé';
                                $statutClass = 'status-completed';
                                break;
                            default:
                                $statutText = 'Inconnu';
                                $statutClass = 'status-unknown';
                        }

                        // Déterminer le type de prêt
                        $typePretText = '';
                        switch ($pret['type_pret_id']) {
                            case 1:
                                $typePretText = 'Prêt Personnel';
                                break;
                            case 2:
                                $typePretText = 'Prêt Auto';
                                break;
                            case 3:
                                $typePretText = 'Prêt Immobilier';
                                break;
                            default:
                                $typePretText = 'Autre';
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($pret['id']) ?></td>
                            <td>
                                <div class="client-info">
                                    <strong><?= htmlspecialchars($nomClient) ?></strong>
                                    <small>ID: <?= htmlspecialchars($pret['client_id']) ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="montant"><?= number_format($pret['montant'], 2, ',', ' ') ?> Ar</span>
                            </td>
                            <td><?= htmlspecialchars($typePretText) ?></td>
                            <td><?= date('d/m/Y', strtotime($pret['date_demande'])) ?></td>
                            <td><?= htmlspecialchars($pret['duree_mois']) ?> mois</td>
                            <td>
                                <span class="status <?= $statutClass ?>"><?= $statutText ?></span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-sm btn-secondary" onclick="voirDetails(<?= $pret['id'] ?>)" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="modifierPret(<?= $pret['id'] ?>)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" onclick="validerPret(<?= $pret['id'] ?>)" title="Valider">
                                        <i class="fas fa-clipboard-check"></i>
                                    </button>
                                    <?php if ($pret['id_statut'] == 1): ?>
                                        <button class="btn btn-sm btn-success" onclick="approuverPret(<?= $pret['id'] ?>)" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejeterPret(<?= $pret['id'] ?>)" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <p>Total: <?= count($prets) ?> prêt(s) trouvé(s)</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal pour nouveau prêt -->
<div id="modalPret" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nouveau Prêt</h3>
            <span class="close" onclick="fermerModalPret()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="formPret" onsubmit="soumettreFormulairePret(event)">
                <div class="form-group">
                    <label for="client_id">Client</label>
                    <select id="client_id" name="client_id" required>
                        <option value="">Sélectionnez un client</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>">
                                <?= htmlspecialchars($client['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="montant">Montant (Ar)</label>
                    <input type="number" id="montant" name="montant" step="0.01" min="1" required>
                </div>

                <div class="form-group">
                    <label for="type_pret_id">Type de Prêt</label>
                    <select id="type_pret_id" name="type_pret_id" required>
                        <option value="">Sélectionnez un type</option>
                        <option value="1">Prêt Personnel</option>
                        <option value="2">Prêt Auto</option>
                        <option value="3">Prêt Immobilier</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_demande">Date de Demande</label>
                    <input type="date" id="date_demande" name="date_demande" required>
                </div>

                <div class="form-group">
                    <label for="duree_mois">Durée (mois)</label>
                    <input type="number" id="duree_mois" name="duree_mois" min="1" max="360" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="fermerModalPret()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le Prêt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ...existing code... -->

<style>
/* Styles pour la page liste des prêts */
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.content-header h2 {
    color: #2c3e50;
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.content-body {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Boutons */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    margin: 2px;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #1e7e34;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* État vide */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 20px;
    color: #dee2e6;
}

.empty-state h3 {
    color: #495057;
    margin: 0 0 10px 0;
}

/* Tableau */
.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.table th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Info client */
.client-info strong {
    display: block;
    color: #2c3e50;
    font-weight: 600;
}

.client-info small {
    color: #6c757d;
    font-size: 11px;
}

/* Montant */
.montant {
    font-weight: 600;
    color: #28a745;
    font-size: 15px;
}

/* Statuts */
.status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-approved {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-ongoing {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-completed {
    background-color: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

.status-unknown {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

/* Actions */
.actions {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
}

/* Footer du tableau */
.table-footer {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
    color: #6c757d;
    font-size: 13px;
}

.table-footer p {
    margin: 0;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
}

.close {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

/* Formulaires */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: #2c3e50;
    font-weight: 500;
    font-size: 14px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

/* Responsive */
@media (max-width: 768px) {
    .content-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .actions {
        justify-content: center;
    }
    
    .table-container {
        font-size: 12px;
    }
    
    .table th,
    .table td {
        padding: 8px 4px;
    }
    
    .modal-content {
        margin: 10% auto;
        width: 95%;
    }
}
</style>

<script>
const apiBase = "http://localhost<?= BASE_URL ?>";

function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
            callback(JSON.parse(xhr.responseText));
        }
    };
    xhr.send(data);
}

function ouvrirModalPret() {
    document.getElementById('modalPret').style.display = 'block';
    // Définir la date du jour par défaut
    document.getElementById('date_demande').value = new Date().toISOString().split('T')[0];
}

function fermerModalPret() {
    document.getElementById('modalPret').style.display = 'none';
    document.getElementById('formPret').reset();
}

function validerPret(pretId) {
    if (confirm('Êtes-vous sûr de vouloir valider ce prêt ?')) {
        const data = JSON.stringify({
            pret_id: pretId
        });
        
        ajax('POST', '/pret/valider', data, function(response) {
            if (response.success) {
                alert('Prêt validé avec succès !');
                // Recharger la page pour voir les changements
                location.reload();
            } else {
                alert('Erreur lors de la validation : ' + (response.message || 'Erreur inconnue'));
            }
        });
    }
}

// ...existing code...
</script>