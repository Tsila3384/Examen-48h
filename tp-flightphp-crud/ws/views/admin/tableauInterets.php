<div class="content-body">
    <!-- Filtres de dates -->
    <div class="filters-section">
        <form id="formFiltre" onsubmit="return false;">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut" 
                           value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
                </div>
                <div class="filter-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin" 
                           value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
                </div>
                <div class="filter-actions">
                    <button type="button" class="btn btn-primary" onclick="appliquerFiltre()">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="viderFiltres()">
                        <i class="fas fa-times"></i> Effacer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Zone de chargement -->
    <div id="loading" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Chargement des données...</p>
        </div>
    </div>

    <!-- Contenu dynamique -->
    <div id="tableau-content">
    </div>

    <!-- Section graphique -->
    <div id="graphique-section" class="chart-section" style="display: none;">
        <div class="chart-header">
            <h3>Évolution des Intérêts par Mois</h3>
        </div>
        <div class="chart-container">
            <canvas id="interetsChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<style>
/* Styles pour la page tableau des intérêts */
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

/* Section des filtres */
.filters-section {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.filters-row {
    display: flex;
    gap: 20px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 180px;
}

.filter-group label {
    font-weight: 500;
    margin-bottom: 5px;
    color: #2c3e50;
}

.filter-group input {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

/* Cartes de résumé */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
    background: #f8f9fa;
}

.summary-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.card-content h3 {
    margin: 0;
    font-size: 24px;
    color: #2c3e50;
    font-weight: 600;
}

.card-content p {
    margin: 5px 0 0 0;
    color: #6c757d;
    font-size: 14px;
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

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
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

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    margin: 2px;
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

.total-row {
    background-color: #e9ecef !important;
    font-weight: 600;
}

.total-row:hover {
    background-color: #e9ecef !important;
}

/* Styles spécifiques */
.periode {
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
}

.montant {
    font-weight: 600;
    color: #28a745;
    font-size: 15px;
}

/* Barre de pourcentage */
.percentage-bar {
    position: relative;
    width: 100%;
    height: 20px;
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.percentage-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    transition: width 0.3s ease;
}

.percentage-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    font-weight: 600;
    color: #2c3e50;
}

/* Actions */
.actions {
    display: flex;
    gap: 6px;
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

/* Loading overlay */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.loading-spinner {
    text-align: center;
    color: #007bff;
}

.loading-spinner i {
    font-size: 32px;
    margin-bottom: 10px;
}

.loading-spinner p {
    margin: 0;
    font-weight: 500;
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
    margin: 10% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
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

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
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
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .summary-cards {
        grid-template-columns: 1fr;
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
}

/* Styles pour le graphique */
.chart-section {
    margin-top: 30px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chart-header {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.chart-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 20px;
    font-weight: 600;
}

.chart-container {
    padding: 20px;
    position: relative;
    height: 400px;
}

.chart-container canvas {
    max-height: 100%;
    width: 100% !important;
    height: auto !important;
}
</style>

<script>
const apiBase = "http://localhost<?= BASE_URL ?>";

// Variables globales pour le graphique
let interetsChart = null;

function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            } else {
                console.error('Erreur AJAX:', xhr.status);
                masquerLoading();
            }
        }
    };
    xhr.send(data);
}

function afficherLoading() {
    document.getElementById('loading').style.display = 'flex';
}

function masquerLoading() {
    document.getElementById('loading').style.display = 'none';
}

function formatNumber(number, decimales = 2) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: decimales,
        maximumFractionDigits: decimales
    }).format(number);
}

function creerGraphique(donnees) {
    const ctx = document.getElementById('interetsChart').getContext('2d');
    
    // Détruire le graphique existant s'il existe
    if (interetsChart) {
        interetsChart.destroy();
    }
    
    // Préparer les données
    const labels = donnees.map(item => {
        const [annee, mois] = item.AnneeMois.split('-');
        const date = new Date(annee, mois - 1);
        return date.toLocaleDateString('fr-FR', { 
            year: 'numeric', 
            month: 'short' 
        });
    });
    
    const valeurs = donnees.map(item => parseFloat(item.total_mensualites));
    
    // Configuration du graphique
    const config = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant des Intérêts (Ar)',
                data: valeurs,
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution des Revenus d\'Intérêts par Mois',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   formatNumber(context.parsed.y) + ' Ar';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (Ar)'
                    },
                    ticks: {
                        callback: function(value) {
                            return formatNumber(value, 0) + ' Ar';
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Période'
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    };
    
    interetsChart = new Chart(ctx, config);
    
    // Afficher la section graphique
    document.getElementById('graphique-section').style.display = 'block';
}

function genererContenuTableau(interets) {
    if (!interets || interets.length === 0) {
        // Cacher le graphique si pas de données
        document.getElementById('graphique-section').style.display = 'none';
        
        return `<div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <h3>Aucun intérêt trouvé</h3>
            <p>Il n'y a aucune mensualité enregistrée pour la période sélectionnée.</p>
        </div>`;
    }

    // Créer le graphique avec les nouvelles données
    creerGraphique(interets);

    // Calculer les totaux
    let totalGeneral = 0;
    const nombreMois = interets.length;
    interets.forEach(interet => {
        totalGeneral += parseFloat(interet.total_mensualites);
    });
    const moyenneMensuelle = nombreMois > 0 ? totalGeneral / nombreMois : 0;

    // Générer les cartes de résumé
    let html = `<div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="card-content">
                <h3>${formatNumber(totalGeneral)} Ar</h3>
                <p>Total des intérêts</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="card-content">
                <h3>${nombreMois}</h3>
                <p>Mois analysés</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="card-content">
                <h3>${formatNumber(moyenneMensuelle)} Ar</h3>
                <p>Moyenne mensuelle</p>
            </div>
        </div>
    </div>`;

    // Générer le tableau
    html += `<div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Période (Année-Mois)</th>
                    <th>Total des Mensualités</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>`;

    interets.forEach(interet => {
        const pourcentage = totalGeneral > 0 ? (parseFloat(interet.total_mensualites) / totalGeneral) * 100 : 0;
        html += `<tr>
            <td>
                <span class="periode">${interet.AnneeMois}</span>
            </td>
            <td>
                <span class="montant">${formatNumber(parseFloat(interet.total_mensualites))} Ar</span>
            </td>
            <td>
                <div class="actions">
                    <button class="btn btn-sm btn-info" onclick="voirDetailsMois('${interet.AnneeMois}')" title="Voir détails">
                        <i class="fas fa-eye"></i> Détails
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="exporterMois('${interet.AnneeMois}')" title="Exporter">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </td>
        </tr>`;
    });

    html += `</tbody>
            <tfoot>
                <tr class="total-row">
                    <td><strong>TOTAL GÉNÉRAL</strong></td>
                    <td><strong>${formatNumber(totalGeneral)} Ar</strong></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="exporterTout()" title="Exporter tout">
                            <i class="fas fa-file-excel"></i> Export Global
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="table-footer">
        <p>Période analysée : ${nombreMois} mois | Total des revenus : ${formatNumber(totalGeneral)} Ar</p>
    </div>`;

    return html;
}

function chargerDonnees(dateDebut = '', dateFin = '') {
    afficherLoading();
    
    let params = '';
    if (dateDebut || dateFin) {
        const urlParams = new URLSearchParams();
        if (dateDebut) urlParams.append('date_debut', dateDebut);
        if (dateFin) urlParams.append('date_fin', dateFin);
        params = '?' + urlParams.toString();
    }
    
    const url = '/admin/interets/ajax' + params;
    
    ajax('GET', url, null, function(response) {
        masquerLoading();
        if (response.success) {
            const html = genererContenuTableau(response.data);
            document.getElementById('tableau-content').innerHTML = html;
        } else {
            console.error('Erreur lors du chargement des données');
            document.getElementById('tableau-content').innerHTML = `<div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Erreur de chargement</h3>
                <p>Impossible de charger les données. Veuillez réessayer.</p>
            </div>`;
        }
    });
}

function appliquerFiltre() {
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;
    chargerDonnees(dateDebut, dateFin);
}

function viderFiltres() {
    document.getElementById('date_debut').value = '';
    document.getElementById('date_fin').value = '';
    chargerDonnees();
}

function voirDetailsMois(anneeMois) {
    alert('Fonctionnalité "Voir détails" à implémenter pour la période : ' + anneeMois);
}

function exporterMois(anneeMois) {
    alert('Fonctionnalité "Exporter" à implémenter pour la période : ' + anneeMois);
}

function exporterTout() {
    alert('Fonctionnalité "Export global" à implémenter');
}

// Charger les données initiales au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Charger Chart.js depuis CDN
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
    script.onload = function() {
        console.log('Chart.js chargé avec succès');
        // Charger les données une fois Chart.js disponible
        const dateDebut = document.getElementById('date_debut').value;
        const dateFin = document.getElementById('date_fin').value;
        chargerDonnees(dateDebut, dateFin);
    };
    script.onerror = function() {
        console.error('Erreur lors du chargement de Chart.js');
        // Charger quand même les données sans graphique
        const dateDebut = document.getElementById('date_debut').value;
        const dateFin = document.getElementById('date_fin').value;
        chargerDonnees(dateDebut, dateFin);
    };
    document.head.appendChild(script);
});
</script>
