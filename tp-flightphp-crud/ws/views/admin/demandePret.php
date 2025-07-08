<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Prêt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8fafc;
            line-height: 1.6;
            color: #334155;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .form-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: none;
        }

        .alert.success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert.error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .info-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-box h3 {
            color: #1e40af;
            margin-bottom: 0.5rem;
        }

        .info-box p {
            color: #1e40af;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouvelle Demande de Prêt</h1>
            <p>Remplissez le formulaire ci-dessous pour créer une demande de prêt</p>
        </div>

        <div id="alert" class="alert"></div>

        <div class="info-box">
            <h3>📋 Instructions</h3>
            <p>Sélectionnez le client, le type de prêt et remplissez les informations nécessaires. Le système vérifiera automatiquement la disponibilité des fonds.</p>
        </div>

        <div class="form-container">
            <form id="pretForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="client_id">Client *</label>
                        <select id="client_id" name="client_id" required>
                            <option value="">Sélectionner un client</option>
                            <?php if (!empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom']) ?> (<?= htmlspecialchars($client['email']) ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_pret_id">Type de Prêt *</label>
                        <select id="type_pret_id" name="type_pret_id" required>
                            <option value="">Sélectionner un type</option>
                            <?php if (!empty($typesPret)): ?>
                                <?php foreach ($typesPret as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['nom']) ?> (Max: <?= $type['duree_max'] ?> mois)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="montant">Montant (€) *</label>
                        <input type="number" id="montant" name="montant" min="1000" step="100" required placeholder="Ex: 50000">
                    </div>

                    <div class="form-group">
                        <label for="duree">Durée (mois) *</label>
                        <input type="number" id="duree" name="duree" min="6" max="360" required placeholder="Ex: 120">
                    </div>

                    <div class="form-group">
                        <label for="date_debut">Date de début *</label>
                        <input type="date" id="date_debut" name="date_debut" required>
                    </div>

                    <div class="form-group">
                        <label for="taux_assurance">Taux d'assurance (%)</label>
                        <input type="number" id="taux_assurance" name="taux_assurance" min="0" max="10" step="0.1" value="1.5" placeholder="Ex: 1.5">
                    </div>

                    <div class="form-group full-width">
                        <label for="delai_premier_remboursement">Délai avant premier remboursement (mois)</label>
                        <input type="number" id="delai_premier_remboursement" name="delai_premier_remboursement" min="0" max="12" value="1" placeholder="Ex: 1">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le Prêt</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('pretForm');
        const alert = document.getElementById('alert');

        // Définir la date du jour par défaut
        document.getElementById('date_debut').value = new Date().toISOString().split('T')[0];

        function showAlert(message, type) {
            alert.textContent = message;
            alert.className = `alert ${type}`;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        // Validation en temps réel
        const montantInput = document.getElementById('montant');
        const dureeInput = document.getElementById('duree');
        
        montantInput.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value && value >= 1000) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });

        dureeInput.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value && value >= 6 && value <= 360) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Validations
            if (!data.client_id) {
                showAlert('Veuillez sélectionner un client', 'error');
                return;
            }

            if (!data.type_pret_id) {
                showAlert('Veuillez sélectionner un type de prêt', 'error');
                return;
            }

            if (data.montant < 1000) {
                showAlert('Le montant minimum est de 1 000 €', 'error');
                return;
            }

            if (data.duree < 6 || data.duree > 360) {
                showAlert('La durée doit être entre 6 et 360 mois', 'error');
                return;
            }

            try {
                const response = await fetch('<?= BASE_URL ?>/admin/prets/nouveau', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = '<?= BASE_URL ?>/pret/listePret';
                    }, 2000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur de communication avec le serveur', 'error');
                console.error('Erreur:', error);
            }
        });
    </script>
</body>
</html>
