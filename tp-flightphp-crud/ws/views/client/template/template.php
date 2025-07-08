<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client Bancaire</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/template.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header avec toggle menu -->
    <header class="header">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        <h1>Espace Client Bancaire</h1>
        <div class="user-info">
            <span>Bienvenue</span>
        </div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
            <button class="close-btn" onclick="toggleSidebar()">×</button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?= BASE_URL ?>/client/dashboard" class="menu-item">
                <i class="icon fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a></li>
            <li><a href="<?= BASE_URL ?>/client/prets/formulairePret" class="menu-item">
                <i class="icon fas fa-file-signature"></i>
                <span>Demandes de prêt</span>
            </a></li>
            <li><a href="<?= BASE_URL ?>/client/pret/simuler" class="menu-item">
                <i class="icon fas fa-calculator"></i>
                <span>Simuler un prêt</span>
            </a></li>
            <li><a href="<?= BASE_URL ?>/client/simulations" class="menu-item">
                <i class="icon fas fa-list"></i>
                <span>Mes simulations</span>
            <li><a href="<?= BASE_URL ?>/client/types-pret" class="menu-item">
                <i class="icon fas fa-list-alt"></i>
                <span>Types de prêt</span>
            </a></li>
            <li><a href="<?= BASE_URL ?>/user/listePret" class="menu-item">
                <i class="icon fas fa-history"></i>
                <span>Historique des prêts</span>
            </a></li>
            <li><a href="<?= BASE_URL ?>/auth/deconnexion" class="menu-item logout">
                <i class="icon fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a></li>
        </ul>
    </nav>

    <!-- Overlay pour mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Contenu principal -->
    <main class="main-content">
        <?php
        if(isset($page) && $page != null) {
            include __DIR__ . '/../' . $page.'.php';
        }
        ?>
    </main>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #273267;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .menu-toggle {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 5px;
            margin-right: 15px;
        }

        .hamburger-line {
            width: 25px;
            height: 3px;
            background-color: white;
            transition: 0.3s;
        }

        .header h1 {
            font-size: 1.5rem;
            flex-grow: 1;
        }

        .user-info {
            font-size: 0.9rem;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100vh;
            background: linear-gradient(180deg, #273267 0%, #273267 100%);
            color: white;
            transition: left 0.3s ease;
            z-index: 1001;
            overflow-y: auto;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #273267;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-header h3 {
            font-size: 1.3rem;
            color: #ecf0f1;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .close-btn:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background-color: rgba(255,255,255,0.1);
            border-left-color: #3498db;
            transform: translateX(5px);
        }

        .menu-item.logout:hover {
            background-color: rgba(231, 76, 60, 0.2);
            border-left-color: #e74c3c;
        }

        .menu-item .icon {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        .menu-item span {
            font-size: 0.95rem;
        }

        /* Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .sidebar.active ~ .sidebar-overlay {
            opacity: 1;
            visibility: visible;
        }

        /* Main content */
        .main-content {
            margin-top: 60px;
            padding: 20px;
            min-height: calc(100vh - 60px);
            background-color: #f8f9fa;
        }

        /* Responsive */
        @media (min-width: 1024px) {
            .header {
                left: 300px;
            }
            
            .sidebar {
                left: 0;
            }
            
            .main-content {
                margin-left: 300px;
            }
            
            .menu-toggle {
                display: none;
            }
            
            .sidebar-overlay {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.2rem;
            }
            
            .sidebar {
                width: 280px;
                left: -280px;
            }
            
            .main-content {
                padding: 15px;
            }
        }
    </style>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Fermer la sidebar en cliquant en dehors
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth < 1024 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Gérer le redimensionnement
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('active');
            }
        });

        // Marquer le lien actif
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                if (item.getAttribute('href') && currentPath.includes(item.getAttribute('href'))) {
                    item.style.backgroundColor = 'rgba(52, 152, 219, 0.3)';
                    item.style.borderLeftColor = '#3498db';
                }
            });
        });
    </script>
</body>
</html>