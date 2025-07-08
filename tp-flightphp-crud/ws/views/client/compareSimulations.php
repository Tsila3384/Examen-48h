<div class="comparison-container">
    <h1>Comparaison des simulations</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <div class="comparison-layout">
            <?php foreach ($simulations as $index => $simulation): ?>
                <div class="simulation-column">
                    <h2>Simulation <?= $index + 1 ?></h2>
                    <div class="simulation-details">
                        <div class="detail-item">
                            <span class="detail-label">Montant emprunté</span>
                            <span class="detail-value"><?= number_format($simulation['montant'], 2) ?> Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Durée</span>
                            <span class="detail-value"><?= $simulation['duree_mois'] ?> mois</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Taux d'intérêt</span>
                            <span class="detail-value"><?= $simulation['taux_interet'] ?>%</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Taux d'assurance</span>
                            <span class="detail-value"><?= $simulation['taux_assurance'] ?>%</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Type de prêt</span>
                            <span class="detail-value"><?= htmlspecialchars($simulation['type_pret_nom'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Mensualité totale</span>
                            <span class="detail-value"><?= number_format($simulation['mensualite_totale'], 2) ?> Ar</span>
                        </div>
                        <div class="detail-item highlight">
                            <span class="detail-label">Coût total du crédit</span>
                            <span class="detail-value"><?= number_format($simulation['cout_total'], 2) ?> Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Coût total assurance</span>
                            <span class="detail-value"><?= number_format($simulation['cout_assurance'], 2) ?> Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date de simulation</span>
                            <span class="detail-value"><?= $simulation['date_simulation'] ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="comparison-actions">
            <a href="<?= BASE_URL ?>/client/simulations" class="btn-primary">
                <span class="btn-icon"><i class="fas fa-arrow-left"></i></span>
                Retour aux simulations
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .comparison-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .comparison-container h1 {
        color: #333;
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.2em;
        font-weight: 600;
    }

    .comparison-layout {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .simulation-column {
        flex: 1;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .simulation-column h2 {
        color: #273267;
        font-size: 1.5em;
        margin-bottom: 20px;
        text-align: center;
    }

    .simulation-details {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .detail-item.highlight {
        background-color: #e8f0fe;
        font-weight: 600;
    }

    .detail-label {
        color: #555;
        font-weight: 500;
    }

    .detail-value {
        color: #333;
        font-weight: 400;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
    }

    .comparison-actions {
        margin-top: 20px;
        text-align: center;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 1em;
        font-weight: 500;
        transition: background-color 0.3s ease;
        display: inline-block;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .comparison-layout {
            flex-direction: column;
        }

        .simulation-column {
            margin-bottom: 20px;
        }
    }
</style>