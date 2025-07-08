
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .container {
            max-width: 32rem;
            width: 100%;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
        }
        
        h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        /* Alertes */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            border: 1px solid;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        
        /* Formulaire */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background-color: #f9fafb;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background-color: white;
        }
        
        .form-control:hover {
            border-color: #9ca3af;
            background-color: white;
        }
        
        /* Boutons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            text-align: center;
            margin-right: 0.75rem;
            margin-top: 1rem;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                margin: 0.5rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .btn {
                width: 100%;
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
            
            .btn:last-child {
                margin-bottom: 0;
            }
        }
        
        /* Animation d'entrée */
        .container {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Focus visible pour l'accessibilité */
        .btn:focus-visible {
            outline: 2px solid;
            outline-offset: 2px;
        }
        
        .btn-primary:focus-visible {
            outline-color: #3b82f6;
        }
        
        .btn-secondary:focus-visible {
            outline-color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modifier le Type de Prêt</h2>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/admin/types-pret/update/<?= $type['id'] ?>" method="post">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($type['nom']) ?>" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Durée Max (mois)</label>
                <input type="number" name="duree_max" value="<?= htmlspecialchars($type['duree_max']) ?>" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="<?= BASE_URL ?>/admin/types-pret" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
