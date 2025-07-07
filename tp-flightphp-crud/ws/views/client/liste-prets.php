<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prêts</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Boutons */
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            text-align: center;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }

        .mb-3 {
            margin-bottom: 1.5rem;
        }

        /* Tableau */
        .table {
            width: 100%;
            background-color: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .table thead {
            background-color: #f1f5f9;
        }

        .table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table td {
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges pour statuts */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
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

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            h2 {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .table {
                font-size: 0.875rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Gestion des Prêts</h2>
        <?php if (!empty($prets)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date demande</th>
                        <th>Durée (mois)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prets as $pret): ?>
                        <tr>
                            <td><?= isset($pret['id']) ? htmlspecialchars($pret['id']) : 'N/A' ?></td>
                            <td><?= isset($pret['client_nom']) ? htmlspecialchars($pret['client_nom']) : 'N/A' ?></td>
                            <td><?= isset($pret['type_pret_nom']) ? htmlspecialchars($pret['type_pret_nom']) : 'N/A' ?></td>
                            <td><?= isset($pret['montant']) ? number_format($pret['montant'], 2, ',', ' ') : '0,00' ?> €</td>
                            <td>
                                <span class="badge <?= isset($pret['statut_id']) ?
                                    ($pret['statut_id'] == 1 ? 'badge-success' :
                                        ($pret['statut_id'] == 2 ? 'badge-warning' : 'badge-danger'))
                                    : 'badge-warning' ?>">
                                    <?= isset($pret['statut_nom']) ? htmlspecialchars($pret['statut_nom']) : 'N/A' ?>
                                </span>
                            </td>
                            <td><?= isset($pret['date_demande']) ? date('d/m/Y', strtotime($pret['date_demande'])) : 'N/A' ?>
                            </td>
                            <td><?= isset($pret['duree_mois']) ? htmlspecialchars($pret['duree_mois']) : 'N/A' ?></td>
                            <td>
        
                                    <a href="<?= BASE_URL ?>/user/prets/pdf/<?= $pret['id'] ?>"
                                        class="btn btn-sm btn-warning">Generer PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Aucun prêt enregistré</div>
        <?php endif; ?>
    </div>
</body>

</html>