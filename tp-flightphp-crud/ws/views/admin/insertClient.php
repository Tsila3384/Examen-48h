<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insérer un Client</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .form-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1rem;
            width: 100%;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
            margin-top: 1rem;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: none;
        }

        .alert.success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert.error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Insérer un Nouveau Client</h1>
            <p>Remplissez le formulaire ci-dessous pour créer un nouveau client</p>
        </div>

        <div id="alert" class="alert"></div>

        <div class="form-container">
            <form id="clientForm">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom" required placeholder="Ex: Jean Dupont">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required placeholder="Ex: jean.dupont@email.com">
                </div>

                <div class="form-group">
                    <label for="username">Nom d'utilisateur *</label>
                    <input type="text" id="username" name="username" required placeholder="Ex: jdupont">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" required placeholder="Min. 6 caractères">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Répétez le mot de passe">
                    </div>
                </div>

                <div class="form-group">
                    <label for="salaire">Salaire mensuel (€) *</label>
                    <input type="number" id="salaire" name="salaire" min="1" step="0.01" required placeholder="Ex: 2500">
                </div>

                <button type="submit" class="btn btn-primary">
                    Créer le Client
                </button>

                <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-secondary">
                    Retour au Dashboard
                </a>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('clientForm');
        const alert = document.getElementById('alert');

        function showAlert(message, type) {
            alert.textContent = message;
            alert.className = `alert ${type}`;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        // Validation en temps réel
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const salaire = document.getElementById('salaire');

        password.addEventListener('input', function() {
            if (this.value.length >= 6) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });

        confirmPassword.addEventListener('input', function() {
            if (this.value === password.value && this.value.length > 0) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });

        salaire.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value && value > 0) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Validations côté client
            if (data.password !== data.confirm_password) {
                showAlert('Les mots de passe ne correspondent pas', 'error');
                return;
            }

            if (data.password.length < 6) {
                showAlert('Le mot de passe doit contenir au moins 6 caractères', 'error');
                return;
            }

            if (!data.email.includes('@')) {
                showAlert('Email invalide', 'error');
                return;
            }

            if (parseFloat(data.salaire) <= 0) {
                showAlert('Le salaire doit être supérieur à 0', 'error');
                return;
            }

            try {
                const response = await fetch('<?= BASE_URL ?>/admin/clients/nouveau', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    form.reset();
                    setTimeout(() => {
                        window.location.href = '<?= BASE_URL ?>/admin/dashboard';
                    }, 2000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur de communication avec le serveur', 'error');
                console.error('Erreur:', error);
            }
        });
    </script>
</body>
</html>
