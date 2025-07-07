<!DOCTYPE html>
<<<<<<< Updated upstream
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des types de prêt</title>
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
        
        /* Actions dans le tableau */
        .table td:last-child {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .table td:last-child form {
            display: inline;
        }
        
        /* Alerte */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            font-weight: 500;
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
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
            
            .table td:last-child {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .btn-sm {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .table th,
            .table td {
                min-width: 120px;
            }
            
            .table td:last-child {
                min-width: 200px;
            }
        }
        
        /* Animation pour les boutons */
        .btn:active {
            transform: translateY(0);
        }
        
        /* Confirmation de suppression */
        .btn-danger:hover {
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestion des types de prêt</h2>
        
        <a href="<?= BASE_URL ?>/admin/types-pret/create" class="btn btn-primary mb-3">Nouveau type</a>
        
        <?php if (!empty($types)) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Durée max</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $type) : ?>
                        <tr>
                            <td><?= htmlspecialchars($type['id']) ?></td>
                            <td><?= htmlspecialchars($type['nom']) ?></td>
                            <td><?= htmlspecialchars($type['duree_max']) ?> mois</td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/types-pret/edit/<?= $type['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="<?= BASE_URL ?>/admin/types-pret/delete/<?= $type['id'] ?>" method="POST" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type de prêt ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="alert alert-info">Aucun type de prêt enregistré</div>
        <?php endif; ?>
    </div>
=======
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

>>>>>>> Stashed changes
</body>
</html>