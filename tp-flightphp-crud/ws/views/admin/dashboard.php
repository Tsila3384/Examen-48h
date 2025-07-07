<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
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

        .info-section {
            margin-bottom: 30px;
        }

        .info-section h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .info-section p {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
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
            <h1>Bienvenue, <?= htmlspecialchars($_SESSION['username'] ?? 'Administrateur') ?> !</h1>
            <p>Tableau de bord d'administration bancaire</p>
        </div>

        <div id="alert" class="alert"></div>

        <div class="info-section">
            <h2>Informations administrateur</h2>
            <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($_SESSION['role'] ?? 'N/A') ?></p>
            <p><strong>Statut :</strong> Administrateur système</p>
            <!-- Placeholder for additional admin data -->
            <p><strong>Actions disponibles :</strong> Gestion des utilisateurs, fonds, prêts et rapports</p>
        </div>

        <form id="logoutForm" action="<?= BASE_URL ?>/auth/deconnexion" method="POST">
            <button type="submit" class="btn">Se déconnecter</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('logoutForm');
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
            
            try {
                const response = await fetch('<?= BASE_URL ?>/auth/deconnexion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion au serveur', 'error');
            }
        });
    </script>
</body>
</html>