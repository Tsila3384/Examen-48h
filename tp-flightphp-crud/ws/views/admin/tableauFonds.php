<div class="tableau-fonds">
    <h1>Tableau des Fonds Disponibles par Mois</h1>
    
    <div class="filtres">
        <label for="dateDebut">Date de début :</label>
        <input type="month" id="dateDebut" name="dateDebut">
        
        <label for="dateFin">Date de fin :</label>
        <input type="month" id="dateFin" name="dateFin">
        
        <button type="button" class="btn-primary" onclick="chargerFonds()">Filtrer</button>
        <button type="button" class="btn-secondary" onclick="reinitialiserFiltres()">Réinitialiser</button>
    </div>
    
    <div id="summary" class="summary" style="display: none;">
        <div class="summary-card">
            <h3>Total Mensualités</h3>
            <div class="value" id="totalMensualites">0 €</div>
        </div>
        <div class="summary-card">
            <h3>Fonds Disponibles</h3>
            <div class="value" id="fondsDisponibles">0 €</div>
        </div>
        <div class="summary-card">
            <h3>Total Combiné</h3>
            <div class="value" id="totalCombine">0 €</div>
        </div>
    </div>
    
    <div id="loading" class="loading" style="display: none;">
        Chargement des données...
    </div>
    
    <div id="error" class="error" style="display: none;"></div>
    
    <div class="tableau-responsive">
        <table class="table" id="tableauFonds">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Total Mensualités</th>
                    <th>Fonds Disponibles</th>
                    <th>Total (Mensualités + Fonds)</th>
                </tr>
            </thead>
            <tbody id="tbodyFonds">
                <!-- Les données seront chargées via Ajax -->
            </tbody>
        </table>
    </div>
</div>

<style>
        .tableau-fonds {
            margin: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filtres {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .filtres input, .filtres button {
            margin: 5px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        .tableau-responsive {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .table tr:hover {
            background-color: #f5f5f5;
        }
        
        .montant {
            text-align: right;
            font-weight: bold;
        }
        
        .montant.positif {
            color: #28a745;
        }
        
        .montant.negatif {
            color: #dc3545;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .error {
            color: #dc3545;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            flex: 1;
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
            text-align: center;
        }
        
        .summary-card h3 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 14px;
        }
        
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
    </style>

    <script>
        // Fonction pour charger les fonds via Ajax
        function chargerFonds() {
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const summary = document.getElementById('summary');
            const tbody = document.getElementById('tbodyFonds');
            
            // Afficher le loading
            loading.style.display = 'block';
            error.style.display = 'none';
            summary.style.display = 'none';
            
            // Récupérer les valeurs des filtres
            const dateDebut = document.getElementById('dateDebut').value;
            const dateFin = document.getElementById('dateFin').value;
            
            // Construire l'URL avec les paramètres
            let url = '<?= BASE_URL ?>/admin/fonds/ajax';
            const params = new URLSearchParams();
            
            // Debug: afficher l'URL complète
            console.log('URL de base:', url);
            console.log('BASE_URL PHP:', '<?= BASE_URL ?>');
            
            if (dateDebut) {
                params.append('date_debut', dateDebut);
            }
            if (dateFin) {
                params.append('date_fin', dateFin);
            }
            
            if (params.toString()) {
                url += '?' + params.toString();
            }
            
            // Debug: afficher l'URL finale
            console.log('URL finale:', url);
            
            // Faire la requête Ajax
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    
                    if (data.success) {
                        afficherFonds(data.data);
                        calculerResume(data.data);
                    } else {
                        afficherErreur('Erreur lors du chargement des données: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(err => {
                    loading.style.display = 'none';
                    afficherErreur('Erreur de connexion: ' + err.message);
                });
        }
        
        // Fonction pour afficher les données dans le tableau
        function afficherFonds(fonds) {
            const tbody = document.getElementById('tbodyFonds');
            tbody.innerHTML = '';
            
            if (fonds.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Aucune donnée disponible</td></tr>';
                return;
            }
            
            fonds.forEach(fond => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${fond.AnneeMois || '-'}</td>
                    <td class="montant">${formatMontant(fond.total_mensualites || 0)}</td>
                    <td class="montant">${formatMontant(fond.fonds_disponibles || 0)}</td>
                    <td class="montant">${formatMontant(fond.total_mensualites_plus_fonds || 0)}</td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Fonction pour calculer et afficher le résumé
        function calculerResume(fonds) {
            const summary = document.getElementById('summary');
            const totalMensualitesEl = document.getElementById('totalMensualites');
            const fondsDisponiblesEl = document.getElementById('fondsDisponibles');
            const totalCombineEl = document.getElementById('totalCombine');
            
            if (fonds.length === 0) {
                summary.style.display = 'none';
                return;
            }
            
            let totalMensualites = 0;
            let fondsDisponibles = 0;
            let totalCombine = 0;
            
            fonds.forEach(fond => {
                totalMensualites += parseFloat(fond.total_mensualites || 0);
                fondsDisponibles = parseFloat(fond.fonds_disponibles || 0); // Prendre la dernière valeur
                totalCombine += parseFloat(fond.total_mensualites_plus_fonds || 0);
            });
            
            totalMensualitesEl.textContent = formatMontant(totalMensualites);
            fondsDisponiblesEl.textContent = formatMontant(fondsDisponibles);
            totalCombineEl.textContent = formatMontant(totalCombine);
            
            summary.style.display = 'flex';
        }
        
        // Fonction pour formater les montants
        function formatMontant(montant) {
            const nombre = parseFloat(montant);
            if (isNaN(nombre)) return '0 €';
            
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 2
            }).format(nombre);
        }
        
        // Fonction pour afficher les erreurs
        function afficherErreur(message) {
            const error = document.getElementById('error');
            error.textContent = message;
            error.style.display = 'block';
        }
        
        // Fonction pour réinitialiser les filtres
        function reinitialiserFiltres() {
            document.getElementById('dateDebut').value = '';
            document.getElementById('dateFin').value = '';
            chargerFonds();
        }
        
        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            chargerFonds();
        });
    </script>