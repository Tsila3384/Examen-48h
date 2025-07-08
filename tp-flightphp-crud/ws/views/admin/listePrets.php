<a href="<?= BASE_URL ?>/admin/prets/nouveau" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Demande
    </a>
<div class="content-header">
    <h2>Liste des Prêts</h2>
</div>

<div class="content-body">
    <?php if (empty($prets)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h3>Aucun prêt trouvé</h3>
            <p>Il n'y a actuellement aucun prêt enregistré dans le système.</p>
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
                        <th>Taux Assurance (%)</th>
                        <th>Délai 1er Remb. (mois)</th>
                        <th>Statut</th>
                        <th style="width: 300px;">Actions</th>
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
                            <td><?= htmlspecialchars($pret['taux_assurance']) ?> %</td>
                            <td><?= htmlspecialchars($pret['delai_premier_remboursement']) ?> mois</td>
                            <td>
                                <span class="status <?= $statutClass ?>"><?= $statutText ?></span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button onclick="voirDetails(<?= $pret['id'] ?>)" class="btn btn-sm btn-info" title="Voir les détails">
                                        <i class="fas fa-eye"></i> Détails
                                    </button>
                                    <button onclick="telechargerPDF(<?= $pret['id'] ?>)" class="btn btn-sm btn-secondary" title="Télécharger PDF">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <?php if ($pret['id_statut'] == 1): ?>
                                        <button class="btn btn-sm btn-success" onclick="validerPret(<?= $pret['id'] ?>)" title="Valider">
                                            <i class="fas fa-check"></i> Valider
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejeterPret(<?= $pret['id'] ?>)" title="Rejeter">
                                            <i class="fas fa-times"></i> Rejeter
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

<!-- Modal pour les détails du prêt -->
<div id="modalDetails" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>📋 Détails du Prêt</h3>
            <span class="close" onclick="fermerModalDetails()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="detailsContent">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Chargement...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Génération du PDF en cours...</p>
    </div>
</div>


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
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        padding: 6px 12px;
        font-size: 13px;
        margin: 2px;
        min-width: 80px;
        justify-content: center;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #1e7e34;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background-color: #117a8b;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #bd2130;
    }

    /* Actions dans le tableau */
    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
        justify-content: flex-start;
    }

    .actions .btn {
        white-space: nowrap;
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
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: center;
    }

    .actions .btn {
        white-space: nowrap;
        text-align: center;
    }

    .actions .btn i {
        margin-right: 4px;
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
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
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

    .modal-large {
        max-width: 800px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        padding: 1rem;
        border-left: 4px solid #3b82f6;
        background-color: #f8fafc;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    .detail-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 1.125rem;
        color: #1e293b;
        font-weight: 500;
    }

    .loading {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .loading i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loading-content {
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .loading-content i {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 1rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #1e40af;
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
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
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
    const apiBase = "<?= BASE_URL ?>";

    function ajax(method, url, data, callback, errorCallback = null) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        
        if (method === 'POST') {
            xhr.setRequestHeader("Content-Type", "application/json");
        }
        
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        callback(response);
                    } catch (e) {
                        // Si ce n'est pas du JSON, retourner le texte brut
                        callback({ success: true, data: xhr.responseText });
                    }
                } else {
                    if (errorCallback) {
                        errorCallback(xhr);
                    } else {
                        alert('Erreur de communication avec le serveur');
                    }
                }
            }
        };
        
        xhr.send(data);
    }

    function voirDetails(pretId) {
        document.getElementById('modalDetails').style.display = 'block';
        document.getElementById('detailsContent').innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i> Chargement des détails...
            </div>
        `;

        // Charger les détails via AJAX
        ajax('GET', apiBase + '/admin/prets/details/' + pretId + '?ajax=1', null, function(response) {
            if (response.success) {
                afficherDetails(response.data);
            } else {
                document.getElementById('detailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        Erreur lors du chargement des détails : ${response.message || 'Erreur inconnue'}
                    </div>
                `;
            }
        }, function(xhr) {
            document.getElementById('detailsContent').innerHTML = `
                <div class="alert alert-danger">
                    Erreur de communication avec le serveur (${xhr.status})
                </div>
            `;
        });
    }

    function afficherDetails(pretDetails) {
        const statutBadge = getStatutBadge(pretDetails.statut);
        
        const html = `
            <div class="details-content">
                <h4 class="section-title">Informations générales</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">ID du Prêt</div>
                        <div class="detail-value">#${pretDetails.pret_id || 'N/A'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Montant</div>
                        <div class="detail-value">${formatMoney(pretDetails.montant_pret || 0)} €</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Type de Prêt</div>
                        <div class="detail-value">${pretDetails.type_pret || 'N/A'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Statut</div>
                        <div class="detail-value">${statutBadge}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date de demande</div>
                        <div class="detail-value">${formatDate(pretDetails.date_demande)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Durée</div>
                        <div class="detail-value">${pretDetails.duree_mois || 'N/A'} mois</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Taux d'intérêt</div>
                        <div class="detail-value">${pretDetails.taux_interet || 'N/A'}%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Mensualité</div>
                        <div class="detail-value">${formatMoney(pretDetails.montant || 0)} €</div>
                    </div>
                </div>

                <h4 class="section-title">Informations client</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nom du client</div>
                        <div class="detail-value">${pretDetails.client_nom || 'N/A'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">${pretDetails.client_email || 'N/A'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Salaire</div>
                        <div class="detail-value">${formatMoney(pretDetails.client_salaire || 0)} €</div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('detailsContent').innerHTML = html;
    }

    function getStatutBadge(statut) {
        let badgeClass = 'badge-info';
        switch(statut) {
            case 'Approuvé':
                badgeClass = 'badge-success';
                break;
            case 'Rejeté':
                badgeClass = 'badge-danger';
                break;
            case 'En attente':
                badgeClass = 'badge-warning';
                break;
        }
        return `<span class="badge ${badgeClass}">${statut || 'N/A'}</span>`;
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR');
    }

    function fermerModalDetails() {
        document.getElementById('modalDetails').style.display = 'none';
    }

    function telechargerPDF(pretId) {
        // Afficher le loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Créer un lien temporaire pour télécharger le PDF
        const link = document.createElement('a');
        link.href = apiBase + '/admin/prets/pdf/' + pretId;
        link.download = `pret_${pretId}.pdf`;
        link.target = '_blank';
        
        // Cacher le loading après un court délai
        setTimeout(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 1000);
        
        // Déclencher le téléchargement
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Fermer les modales en cliquant en dehors
    window.onclick = function(event) {
        const modalDetails = document.getElementById('modalDetails');
        if (event.target === modalDetails) {
            fermerModalDetails();
        }
    }

    // Gestionnaire d'événements pour la touche Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            fermerModalDetails();
        }
    });

    function ouvrirModalPret() {
        document.getElementById('modalPret').style.display = 'block';
        // Définir la date du jour par défaut
        document.getElementById('date_demande').value = new Date().toISOString().split('T')[0];
    }

    function fermerModalPret() {
        document.getElementById('modalPret').style.display = 'none';
        document.getElementById('formPret').reset();
    }

    function rejeterPret(pretId) {
        if (confirm('Êtes-vous sûr de vouloir rejeter ce prêt ?')) {
            const data = JSON.stringify({
                pret_id: pretId
            });
            ajax('POST', apiBase + '/pret/rejeter', data, function(response) {
                if (response.success) {
                    alert('Prêt rejeté avec succès !');
                    location.reload(); // Recharger la page pour voir les changements
                } else {
                    alert('Erreur lors du rejet : ' + (response.message || 'Erreur inconnue'));
                }
            });
        }
    }

    function validerPret(pretId) {
        if (confirm('Êtes-vous sûr de vouloir valider ce prêt ?')) {
            const data = JSON.stringify({
                pret_id: pretId
            });

            ajax('POST', apiBase + '/pret/valider', data, function(response) {
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


</script>