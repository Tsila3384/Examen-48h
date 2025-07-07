<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client Bancaire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            text-align: center;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 80px;
            width: 250px;
            height: calc(100vh - 80px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: calc(100vh - 80px);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
        <h1>Espace Client Bancaire</h1>
    </header>

    <nav class="sidebar" id="sidebar">
        <ul>
            <li><a href="<?= BASE_URL ?>/client/dashboard" class="active">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/client/transactions">Transactions</a></li>
            <li><a href="<?= BASE_URL ?>/client/transfert">Transférer des fonds</a></li>
            <li><a href="<?= BASE_URL ?>/client/prets/formulairePret">Demandes de prêt</a></li>
            <li><a href="<?= BASE_URL ?>/client/profil">Profil</a></li>
            <li><a href="<?= BASE_URL ?>/auth/deconnexion">Déconnexion</a></li>
            <li><a href="<?=  BASE_URL?>/user/listePret">historiques des prets</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <?php
        if(isset($page) && $page != null) {
            include __DIR__ . '/../' . $page.'.php';
        } else {
        }
        ?>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>