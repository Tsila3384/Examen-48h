<div class="prets-container">
    <div id="alert" class="alert" style="display: none;"></div>

    <div class="page-header">
        <h1>Demande de prêt</h1>
        <p>Remplissez le formulaire ci-dessous pour faire votre demande de prêt</p>
    </div>

    <div class="form-container">
        <form id="loanForm" action="<?= BASE_URL ?>/client/pret/demandePret" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="montant">
                        <span class="label-text">Montant du prêt</span>
                        <span class="label-currency">Ar</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="montant" name="montant" min="1000" step="100" required placeholder="Ex: 25 000">
                        <div class="input-icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                    <div class="form-hint">Montant minimum : 1 000Ar</div>
                </div>

                <div class="form-group">
                    <label for="type_pret_id">
                        <span class="label-text">Type de prêt</span>
                    </label>
                    <div class="input-wrapper">
                        <select id="type_pret_id" name="type_pret_id" required>
                            <option value="">Sélectionnez un type de prêt</option>
                            <?php foreach ($typesPret as $type): ?>
                                <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-icon"><i class="fas fa-list-alt"></i></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_debut">
                        <span class="label-text">Date de début</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="date" id="date_debut" name="date_debut" required>
                        <div class="input-icon"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <div class="form-hint">Date de début souhaitée du prêt</div>
                </div>

                <div class="form-group">
                    <label for="duree">
                        <span class="label-text">Durée</span>
                        <span class="label-unit">mois</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="duree" name="duree" min="6" max="360" step="1" required placeholder="Ex: 60">
                        <div class="input-icon"><i class="fas fa-clock"></i></div>
                    </div>
                    <div class="form-hint">Entre 6 et 360 mois</div>
                </div>

                <div class="form-group">
                    <label for="taux_assurance">
                        <span class="label-text">Taux d'assurance</span>
                        <span class="label-unit">%</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="taux_assurance" name="taux_assurance" min="0" step="0.01" required placeholder="Ex: 0.35">
                        <div class="input-icon"><i class="fas fa-shield-alt"></i></div>
                    </div>
                    <div class="form-hint">Taux d'assurance souhaité</div>
                </div>

                <div class="form-group">
                    <label for="delai_premier_remboursement">
                        <span class="label-text">Délai premier remboursement</span>
                        <span class="label-unit">mois</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="delai_premier_remboursement" name="delai_premier_remboursement" min="0" step="1" required placeholder="Ex: 1">
                        <div class="input-icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                    <div class="form-hint">Nombre de mois avant le premier remboursement</div>
                </div>
            </div>

            <input type="hidden" name="client_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span class="btn-icon"><i class="fas fa-file-alt"></i></span>
                    Soumettre la demande
                </button>
                <a href="<?= BASE_URL ?>/client/pret/simuler" class="btn-secondary">
                    <span class="btn-icon"><i class="fas fa-calculator"></i></span>
                    Simuler avant de demander
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const form = document.getElementById('loanForm');
    const alert = document.getElementById('alert');

    function showAlert(message, type) {
        alert.textContent = message;
        alert.className = `alert alert-${type}`;
        alert.style.display = 'block';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }

    // Mise à jour en temps réel des informations
    const montantInput = document.getElementById('montant');
    const dureeInput = document.getElementById('duree');
    
    montantInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        if (value && value >= 1000) {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '#dc3545';
        }
    });

    dureeInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        if (value && value >= 6 && value <= 360) {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '#dc3545';
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        // Validations
        if (data.montant < 1000) {
            showAlert('Le montant doit être d\'au moins 1 000Ar', 'error');
            return;
        }
        if (data.duree < 6 || data.duree > 360) {
            showAlert('La durée doit être entre 6 et 360 mois', 'error');
            return;
        }
        const today = new Date().toISOString().split('T')[0];
        if (data.date_debut < today) {
            showAlert('La date de début ne peut pas être antérieure à aujourd\'hui', 'error');
            return;
        }
        if (data.taux_assurance < 0) {
            showAlert('Le taux d\'assurance ne peut pas être négatif', 'error');
            return;
        }
        if (data.delai_premier_remboursement < 0) {
            showAlert('Le délai de premier remboursement ne peut pas être négatif', 'error');
            return;
        }

        try {
            const response = await fetch('<?= BASE_URL ?>/client/pret/demandePret', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, 'success');
                setTimeout(() => {
                    window.location.href = '<?= BASE_URL ?>/client/dashboard';
                }, 2000);
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur de connexion au serveur', 'error');
        }
    });
</script>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/prets.css">
