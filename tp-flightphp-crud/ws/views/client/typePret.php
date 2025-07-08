<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/list.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-university"></i> Types de Prêts Disponibles</h2>
        <p>Découvrez nos différentes offres de prêts adaptées à vos besoins</p>
    </div>

    <?php if (!empty($types)): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-list-alt"></i> Nom du prêt</th>
                        <th><i class="fas fa-chart-line"></i> Taux d'intérêt</th>
                        <th><i class="fas fa-clock"></i> Durée maximale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $index => $type): ?>
                        <tr style="animation-delay: <?= $index * 0.1 ?>s;">
                            <td>
                                <div class="type-info">
                                    <strong><?= htmlspecialchars($type['nom']) ?></strong>
                                    <small class="type-description">
                                        <?php
                                        // Descriptions dynamiques basées sur le type
                                        $descriptions = [
                                            'Personnel' => 'Prêt flexible pour vos projets personnels',
                                            'Immobilier' => 'Financement pour achat ou construction',
                                            'Auto' => 'Prêt véhicule aux conditions avantageuses',
                                            'Professionnel' => 'Financement pour votre activité'
                                        ];
                                        echo $descriptions[$type['nom']] ?? 'Prêt adapté à vos besoins';
                                        ?>
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="rate-badge <?= $type['taux_interet'] <= 5 ? 'rate-excellent' : ($type['taux_interet'] <= 10 ? 'rate-good' : 'rate-standard') ?>">
                                    <?= htmlspecialchars($type['taux_interet']) ?>%
                                </span>
                            </td>
                            <td>
                                <span class="duration">
                                    <?= htmlspecialchars($type['duree_max']) ?> mois
                                    <small>(<?= number_format($type['duree_max'] / 12, 1) ?> ans)</small>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="info-cards">
            <div class="info-card">
                <h3><i class="fas fa-calculator"></i> Simulation gratuite</h3>
                <p>Calculez votre mensualité et obtenez une estimation immédiate.</p>
                <a href="<?= BASE_URL ?>/simulation" class="btn btn-primary">Simuler un prêt</a>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-info">
            <strong>Aucun type de prêt disponible</strong>
            <p>Aucun type de prêt n'est actuellement disponible pour votre profil. Contactez-nous pour plus d'informations.</p>
        </div>
    <?php endif; ?>
</div>