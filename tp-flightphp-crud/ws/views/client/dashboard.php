<div class="dashboard-container">
    <!-- Alert -->
    <div id="alert" class="alert" style="display: none;"></div>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-card">
            <div class="welcome-header">
                <h1>Bienvenue dans votre espace client</h1>
                <p class="welcome-subtitle">Gérez vos prêts en toute simplicité</p>
            </div>
            <div class="user-info-grid">
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-user"></i></div>
                    <div class="info-content">
                        <span class="info-label">Nom d'utilisateur</span>
                        <span class="info-value"><?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-tag"></i></div>
                    <div class="info-content">
                        <span class="info-label">Rôle</span>
                        <span class="info-value role-badge"><?= htmlspecialchars($_SESSION['role'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-content">
                        <span class="info-label">Dernière connexion</span>
                        <span class="info-value">Aujourd'hui</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="info-content">
                        <span class="info-label">Statut du compte</span>
                        <span class="info-value status-active">Actif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Actions rapides</h2>
        <div class="actions-grid">
            <a href="<?= BASE_URL ?>/client/prets/formulairePret" class="action-card">
                <div class="action-icon"><i class="fas fa-file-signature"></i></div>
                <h3>Nouvelle demande</h3>
                <p>Faire une demande de prêt</p>
            </a>
            <a href="<?= BASE_URL ?>/client/pret/simuler" class="action-card">
                <div class="action-icon"><i class="fas fa-calculator"></i></div>
                <h3>Simuler un prêt</h3>
                <p>Calculer vos mensualités</p>
            </a>
            <a href="<?= BASE_URL ?>/user/listePret" class="action-card">
                <div class="action-icon"><i class="fas fa-history"></i></div>
                <h3>Mes prêts</h3>
                <p>Consulter l'historique</p>
            </a>
            <a href="<?= BASE_URL ?>/client/types-pret" class="action-card">
                <div class="action-icon"><i class="fas fa-list-alt"></i></div>
                <h3>Types de prêts</h3>
                <p>Découvrir nos offres</p>
            </a>
        </div>
    </div>

    <!-- Logout Section -->
    <div class="logout-section">
        <form id="logoutForm" action="<?= BASE_URL ?>/auth/deconnexion" method="POST">
            <button type="submit" class="btn-logout">
                <span class="logout-icon"><i class="fas fa-sign-out-alt"></i></span>
                Se déconnecter
            </button>
        </form>
    </div>
</div>

<script>
    const form = document.getElementById('logoutForm');
    const alert = document.getElementById('alert');

    function showAlert(message, type) {
        alert.textContent = message;
        alert.className = `alert alert-${type}`;
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
                showAlert(data.message, 'danger');
            }
        } catch (error) {
            showAlert('Erreur de connexion au serveur', 'danger');
        }
    });

    // Animation au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.welcome-section, .quick-actions, .statistics-section, .recent-activity').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
</script>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dashboard.css">
