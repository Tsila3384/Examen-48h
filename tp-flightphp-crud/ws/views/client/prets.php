<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Prêt - Client</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Demande de Prêt</h1>
            <p>Remplissez le formulaire pour soumettre une demande de prêt</p>
        </div>

        <div id="alert" class="alert"></div>

        <form id="loanForm" action="<?= BASE_URL ?>/client/pret/demandePret" method="POST">
            <div class="form-group">
                <label for="montant">Montant du prêt (€)</label>
                <input type="number" id="montant" name="montant" min="1000" step="100" required>
            </div>
            <div class="form-group">
                <label for="type_pret_id">Type de prêt</label>
                <select id="type_pret_id" name="type_pret_id" required>
                    <?php foreach ($typesPret as $type): ?>
                        <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" required>
            </div>
            <div class="form-group">
                <label for="duree">Durée (en mois)</label>
                <input type="number" id="duree" name="duree" min="6" max="360" step="1" required>
            </div>
            <input type="hidden" name="client_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
            <button type="submit" class="btn">Soumettre la demande</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('loanForm');
        const alert = document.getElementById('alert');

        function showAlert(message, type) {
            alert.textContent = message;
            alert.className = `alert ${type}`;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Client-side validation
            if (data.montant < 1000) {
                showAlert('Le montant doit être d\'au moins 1000€', 'error');
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
                    }, 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion au serveur', 'error');
            }
        });
    </script>
</body>
</html>