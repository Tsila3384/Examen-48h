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
            max-width: 28rem;
            width: 100%;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
        }
        
        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
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
        
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        input::placeholder {
            color: #9ca3af;
        }
        
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
            text-align: center;
        }
        
        .btn-cancel {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .btn-cancel:hover {
            background-color: #d1d5db;
        }
        
        .btn-submit {
            background-color: #2563eb;
            color: white;
        }
        
        .btn-submit:hover {
            background-color: #1d4ed8;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
            }
            
            .button-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Créer un nouveau type de prêt</h2>
        <form action="<?= BASE_URL ?>/admin/types-pret" method="post">
            <div class="form-group">
                <label for="nom">Nom du type</label>
                <input 
                    type="text" 
                    name="nom" 
                    id="nom"
                    required
                    placeholder="Entrez le nom du type de prêt"
                >
            </div>
            <div class="form-group">
                <label for="duree_max">Durée maximale (mois)</label>
                <input 
                    type="number" 
                    name="duree_max" 
                    id="duree_max"
                    required
                    placeholder="Entrez la durée en mois"
                    min="1"
                >
            </div>
            <div class="button-group">
                <a href="<?= BASE_URL ?>/admin/types-pret" class="btn btn-cancel">
                    Annuler
                </a>
                <button type="submit" class="btn btn-submit">
                    Créer
                </button>
            </div>
        </form>
    </div>