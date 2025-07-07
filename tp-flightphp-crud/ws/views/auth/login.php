<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Banque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Connexion</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur ou Email</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                        <div id="login-error" class="alert alert-danger mt-3 d-none"></div>
                        <div class="mt-3 text-center">
                            <a href="/inscription">Créer un compte client</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
// Injection PHP pour BASE_URL
var BASE_URL = "<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>";
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    fetch(BASE_URL + '/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.role === 'admin') {
                window.location.href = BASE_URL + '/admin/dashboard';
            } else {
                window.location.href = BASE_URL + '/client/dashboard';
            }
        } else {
            document.getElementById('login-error').textContent = data.message || 'Erreur de connexion';
            document.getElementById('login-error').classList.remove('d-none');
        }
    })
    .catch(() => {
        document.getElementById('login-error').textContent = 'Erreur réseau';
        document.getElementById('login-error').classList.remove('d-none');
    });
});
</script>
</body>
</html>
