<div class="simulation-container">
    <div id="alert" class="alert" style="display: none;"></div>

    <div class="page-header">
        <h1>Simulateur de prêt</h1>
        <p>Calculez vos mensualités et le coût total de votre prêt</p>
    </div>

    <div class="simulation-layout">
        <div class="form-container">
            <form id="simulationForm">
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
                        <div class="range-slider">
                            <input type="range" id="montantRange" min="1000" max="10000000" step="1000" value="25000">
                            <div class="range-labels">
                                <span>1kAr</span>
                                <span>10MAr</span>
                            </div>
                        </div>
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
                        <div class="range-slider">
                            <input type="range" id="dureeRange" min="6" max="360" step="6" value="60">
                            <div class="range-labels">
                                <span>6 mois</span>
                                <span>30 ans</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="taux_interet">
                            <span class="label-text">Taux d'intérêt</span>
                            <span class="label-unit">%</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="number" id="taux_interet" name="taux_interet" min="0" step="0.01" required placeholder="Ex: 3.5">
                            <div class="input-icon"><i class="fas fa-percentage"></i></div>
                        </div>
                        <div class="range-slider">
                            <input type="range" id="tauxRange" min="0" max="10" step="0.1" value="3.5">
                            <div class="range-labels">
                                <span>0%</span>
                                <span>10%</span>
                            </div>
                        </div>
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
                        <div class="range-slider">
                            <input type="range" id="assuranceRange" min="0" max="10" step="0.01" value="0.35">
                            <div class="range-labels">
                                <span>0%</span>
                                <span>10%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-simulate">
                    <span class="btn-icon"><i class="fas fa-calculator"></i></span>
                    Calculer ma mensualité
                </button>
            </form>
        </div>

        <div class="result-container">
            <div id="simulationResult" class="simulation-result" style="display: none;">
                <div class="result-header">
                    <h2>Résultat de votre simulation</h2>
                </div>

                <div class="result-main">
                    <div class="main-amount">
                        <span class="amount-label">Votre mensualité</span>
                        <span class="amount-value" id="mensualiteTotale">0 Ar</span>
                    </div>
                </div>

                <div class="result-details">
                    <div class="detail-item">
                        <span class="detail-label">Montant emprunté</span>
                        <span class="detail-value" id="montantPret">0 Ar</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Mensualité hors assurance</span>
                        <span class="detail-value" id="mensualiteSansAssurance">0 Ar</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Assurance mensuelle</span>
                        <span class="detail-value" id="assuranceMensuelle">0 Ar</span>
                    </div>
                    <div class="detail-item highlight">
                        <span class="detail-label">Coût total du crédit</span>
                        <span class="detail-value" id="coutTotal">0 Ar</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Coût total assurance</span>
                        <span class="detail-value" id="coutAssurance">0 Ar</span>
                    </div>
                </div>

                <div class="result-actions">
                    <a href="<?= BASE_URL ?>/client/prets/formulairePret" class="btn-primary">
                        <span class="btn-icon"><i class="fas fa-file-signature"></i></span>
                        Faire une demande
                    </a>
                    <button id="saveSimulation" class="btn-primary">
                        <span class="btn-icon"><i class="fas fa-save"></i></span>
                        Sauvegarder la simulation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('simulationForm');
    const alert = document.getElementById('alert');
    const simulationResult = document.getElementById('simulationResult');

    // Éléments du formulaire
    const montantInput = document.getElementById('montant');
    const dureeInput = document.getElementById('duree');
    const tauxInput = document.getElementById('taux_interet');
    const assuranceInput = document.getElementById('taux_assurance');

    // Sliders
    const montantRange = document.getElementById('montantRange');
    const dureeRange = document.getElementById('dureeRange');
    const tauxRange = document.getElementById('tauxRange');
    const assuranceRange = document.getElementById('assuranceRange');

    // Résultats
    const mensualiteTotale = document.getElementById('mensualiteTotale');
    const montantPret = document.getElementById('montantPret');
    const mensualiteSansAssurance = document.getElementById('mensualiteSansAssurance');
    const assuranceMensuelle = document.getElementById('assuranceMensuelle');
    const coutTotal = document.getElementById('coutTotal');
    const coutAssurance = document.getElementById('coutAssurance');

    // Synchronisation sliders et inputs
    montantRange.addEventListener('input', () => montantInput.value = montantRange.value);
    montantInput.addEventListener('input', () => montantRange.value = montantInput.value);

    dureeRange.addEventListener('input', () => dureeInput.value = dureeRange.value);
    dureeInput.addEventListener('input', () => dureeRange.value = dureeInput.value);

    tauxRange.addEventListener('input', () => tauxInput.value = tauxRange.value);
    tauxInput.addEventListener('input', () => tauxRange.value = tauxInput.value);

    assuranceRange.addEventListener('input', () => assuranceInput.value = assuranceRange.value);
    assuranceInput.addEventListener('input', () => assuranceRange.value = assuranceInput.value);

    function showAlert(message, type) {
        alert.textContent = message;
        alert.className = `alert alert-${type}`;
        alert.style.display = 'block';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }

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
        if (data.taux_interet < 0) {
            showAlert('Le taux d\'intérêt ne peut pas être négatif', 'error');
            return;
        }
        if (data.taux_assurance < 0) {
            showAlert('Le taux d\'assurance ne peut pas être négatif', 'error');
            return;
        }

        try {
            const response = await fetch('<?= BASE_URL ?>/client/pret/simuler', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                const amortissement = result.data;
                const firstMonth = amortissement[0];
                const totalInterets = amortissement.reduce((sum, month) => sum + month.interets, 0);
                const totalAssurance = amortissement.reduce((sum, month) => sum + month.assurance, 0);
                const totalPret = amortissement.reduce((sum, month) => sum + month.mensualite, 0);
                const mensualiteBase = firstMonth.mensualite - firstMonth.assurance;

                montantPret.textContent = `${parseFloat(data.montant).toLocaleString()} Ar`;
                mensualiteSansAssurance.textContent = `${mensualiteBase.toFixed(2)} Ar`;
                assuranceMensuelle.textContent = `${firstMonth.assurance.toFixed(2)} Ar`;
                mensualiteTotale.textContent = `${firstMonth.mensualite.toFixed(2)} Ar`;
                coutTotal.textContent = `${(totalPret).toFixed(2)} Ar`;
                coutAssurance.textContent = `${totalAssurance.toFixed(2)} Ar`;

                simulationResult.style.display = 'block';
                simulationResult.scrollIntoView({
                    behavior: 'smooth'
                });
                showAlert('Simulation effectuée avec succès', 'success');
            } else {
                showAlert(result.message, 'error');
                simulationResult.style.display = 'none';
            }
        } catch (error) {
            showAlert('Erreur de connexion au serveur', 'error');
            simulationResult.style.display = 'none';
        }
    });

    document.getElementById('saveSimulation').addEventListener('click', async () => {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('<?= BASE_URL ?>/client/pret/sauvegarderSimulation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert('Simulation sauvegardée avec succès', 'success');
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur lors de la sauvegarde de la simulation', 'error');
        }
    });
</script>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/prets.css">