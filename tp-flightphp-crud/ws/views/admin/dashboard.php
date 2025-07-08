<!-- Dashboard Container -->
<div class="dashboard-container">

    <!-- Alert Section -->
    <div id="alert" class="alert" style="display: none;"></div>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-card">
            <div class="welcome-header">
                <h1>Bienvenue, <?= htmlspecialchars($_SESSION['username'] ?? 'Administrateur') ?> !</h1>
                <p class="welcome-subtitle">Tableau de bord d'administration bancaire</p>
            </div>

            <div class="user-info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Nom d'utilisateur</span>
                        <span class="info-value"><?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Rôle</span>
                        <span class="info-value role-badge"><?= htmlspecialchars($_SESSION['role'] ?? 'Administrateur') ?></span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Statut</span>
                        <span class="info-value status-active">Actif</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Dernière connexion</span>
                        <span class="info-value"><?= date('d/m/Y H:i') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
        <div class="actions-grid">
            <a href="<?= BASE_URL ?>/user/formulaireFond" class="action-card">
                <div class="action-icon" style="color: #27ae60;">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3>Ajouter des Fonds</h3>
                <p>Créditer des comptes clients ou ajouter des liquidités au système</p>
            </a>

            <a href="<?= BASE_URL ?>/pret/listePret" class="action-card">
                <div class="action-icon" style="color: #e67e22;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>Gérer les Prêts</h3>
                <p>Consulter, approuver ou rejeter les demandes de prêts en cours</p>
            </a>

            <a href="<?= BASE_URL ?>/admin/types-pret" class="action-card">
                <div class="action-icon" style="color: #3498db;">
                    <i class="fas fa-tags"></i>
                </div>
                <h3>Types de Prêt</h3>
                <p>Configurer les différents types de prêts et leurs conditions</p>
            </a>

            <a href="<?= BASE_URL ?>/admin/interets" class="action-card">
                <div class="action-icon" style="color: #9b59b6;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Rapports & Graphiques</h3>
                <p>Visualiser les statistiques et générer des rapports détaillés</p>
            </a>
        </div>
    </div>


    <!-- Logout Section -->
    <div class="logout-section">
        <form id="logoutForm" action="<?= BASE_URL ?>/auth/deconnexion" method="POST">
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt logout-icon"></i>
                Se déconnecter
            </button>
        </form>
    </div>
</div>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dashboard.css">

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