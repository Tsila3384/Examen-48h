<div class="container">
    <div class="page-header">
        <h2>📊 Gestion des Prêts</h2>
        <p>Suivi et gestion de tous vos prêts</p>
    </div>

    <?php if (!empty($prets)): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>🆔 ID</th>
                        <th>👤 Client</th>
                        <th>📋 Type</th>
                        <th>💰 Montant</th>
                        <th>🏷️ Statut</th>
                        <th>📅 Date demande</th>
                        <th>⏱️ Durée</th>
                        <th>⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prets as $index => $pret): ?>
                        <tr style="animation-delay: <?= $index * 0.1 ?>s;">
                            <td>
                                <strong><?= isset($pret['id']) ? htmlspecialchars($pret['id']) : 'N/A' ?></strong>
                            </td>
                            <td>
                                <div class="client-info">
                                    <strong><?= isset($pret['client_nom']) ? htmlspecialchars($pret['client_nom']) : 'N/A' ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge">
                                    <?= isset($pret['type_pret_nom']) ? htmlspecialchars($pret['type_pret_nom']) : 'N/A' ?>
                                </span>
                            </td>
                            <td>
                                <span class="amount">
                                    <?= isset($pret['montant']) ? number_format($pret['montant'], 2, ',', ' ') : '0,00' ?> Ar
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= isset($pret['statut_id']) ?
                                                        ($pret['statut_id'] == 1 ? 'badge-success' : ($pret['statut_id'] == 2 ? 'badge-warning' : 'badge-danger'))
                                                        : 'badge-warning' ?>">
                                    <?= isset($pret['statut_nom']) ? htmlspecialchars($pret['statut_nom']) : 'N/A' ?>
                                </span>
                            </td>
                            <td>
                                <span class="date">
                                    <?= isset($pret['date_demande']) ? date('d/m/Y', strtotime($pret['date_demande'])) : 'N/A' ?>
                                </span>
                            </td>
                            <td>
                                <span class="duration">
                                    <?= isset($pret['duree_mois']) ? htmlspecialchars($pret['duree_mois']) : 'N/A' ?> mois
                                </span>
                            </td>
                            <td>
                                <div class="actions-group">
                                    <a href="<?= BASE_URL ?>/user/prets/pdf/<?= $pret['id'] ?>"
                                        class="btn btn-sm btn-warning"
                                        title="Générer le PDF du prêt">
                                        📄 PDF
                                    </a>
                                    <a href="<?= BASE_URL ?>/user/prets/details/<?= $pret['id'] ?>"
                                        class="btn btn-sm btn-primary"
                                        title="Voir les détails">
                                        👁️ Détails
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="summary-stats">
            <div class="stat-card">
                <h3>📊 Statistiques</h3>
                <p><strong>Total des prêts:</strong> <?= count($prets) ?></p>
                <p><strong>Montant total:</strong>
                    <?= number_format(array_sum(array_column($prets, 'montant')), 2, ',', ' ') ?> Ar
                </p>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-info">
            <strong>Aucun prêt enregistré</strong>
            <p>Vous n'avez encore aucun prêt dans le système.</p>
        </div>
    <?php endif; ?>
</div>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/list.css">