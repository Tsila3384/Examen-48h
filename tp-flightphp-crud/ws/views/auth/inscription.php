<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Système Bancaire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            max-width: 500px;
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

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus {
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

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .links a:hover {
            text-decoration: underline;
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

        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .account-type-info {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #1565c0;
            font-size: 14px;
        }

        .loading {
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Inscription</h1>
            <p>Créez votre compte bancaire</p>
        </div>

        <div class="account-type-info">
            📝 Inscription uniquement pour les clients particuliers
        </div>

        <div id="alert" class="alert"></div>

        <form id="inscriptionForm">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="salaire">Salaire mensuel (Ar)</label>
                <input type="number" id="salaire" name="salaire" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <div class="password-requirements">
                    Minimum 6 caractères
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn" id="submitBtn">S'inscrire</button>
        </form>

        <div class="links">
            <a href="<?= BASE_URL ?>/auth/connexion">Déjà un compte ? Se connecter</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('inscriptionForm');
        const alert = document.getElementById('alert');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const submitBtn = document.getElementById('submitBtn');

        function showAlert(message, type) {
            alert.textContent = message;
            alert.className = `alert ${type}`;
            alert.style.display = 'block';
            
            if (type === 'success') {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 3000);
            } else {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 6000);
            }
        }

        function setLoading(isLoading) {
            if (isLoading) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Inscription en cours...';
                form.classList.add('loading');
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = 'S\'inscrire';
                form.classList.remove('loading');
            }
        }

        // Validation en temps réel
        confirmPassword.addEventListener('input', function() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            // Validation côté client
            if (password.value !== confirmPassword.value) {
                showAlert('Les mots de passe ne correspondent pas', 'error');
                return;
            }

            if (password.value.length < 6) {
                showAlert('Le mot de passe doit contenir au moins 6 caractères', 'error');
                return;
            }

            const salaire = parseFloat(formData.get('salaire'));
            if (isNaN(salaire) || salaire <= 0) {
                showAlert('Le salaire doit être un nombre positif', 'error');
                return;
            }
            
            setLoading(true);
            
            try {
                const response = await fetch('<?=BASE_URL ?>/auth/inscription', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    form.reset();
                    setTimeout(() => {
                        window.location.href = '<?=BASE_URL ?>/auth/connexion';
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion au serveur', 'error');
            } finally {
                setLoading(false);
            }
        });
    </script>
</body>
</html>