<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* === STYLES GÉNÉRAUX === */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #2c3e50;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

/* === CONTAINER PRINCIPAL === */
.container {
    max-width: 1200px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 10px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* === TITRE === */
.container h2 {
    font-size: 2.5rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 40px;
    color: #2c3e50;
    position: relative;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.container h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

/* === TABLEAU === */
.table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 
        0 10px 30px rgba(0, 0, 0, 0.1),
        0 5px 15px rgba(0, 0, 0, 0.05);
}

/* === EN-TÊTE DU TABLEAU === */
.table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table thead tr {
    height: 60px;
}

.table th {
    padding: 20px 25px;
    text-align: left;
    font-weight: 600;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    position: relative;
}

.table th:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 1px;
    height: 50%;
    background: rgba(255, 255, 255, 0.3);
}

/* === CORPS DU TABLEAU === */
.table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.table tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f3f4ff 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
}

.table tbody tr:last-child {
    border-bottom: none;
}

.table td {
    padding: 20px 25px;
    font-size: 1rem;
    font-weight: 500;
    color: #34495e;
    vertical-align: middle;
    border: none;
    position: relative;
}

/* === STYLES SPÉCIAUX POUR LES COLONNES === */
.table td:first-child {
    font-weight: 700;
    color: #2c3e50;
    font-size: 1.1rem;
}

.table td:nth-child(2) {
    color: #7f8c8d;
    font-style: italic;
}

.table td:nth-child(3) {
    color: #e74c3c;
    font-weight: 700;
    font-size: 1.1rem;
}

.table td:nth-child(4) {
    color: #27ae60;
    font-weight: 600;
}

/* === BADGES POUR LES VALEURS === */
.table td:nth-child(3)::before {
    content: '🏦';
    margin-right: 8px;
}

.table td:nth-child(4)::before {
    content: '📅';
    margin-right: 8px;
}

/* === ALERTE === */
.alert {
    padding: 20px 25px;
    margin: 20px 0;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 500;
    text-align: center;
    border: none;
    position: relative;
    overflow: hidden;
}

.alert-info {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    box-shadow: 0 10px 25px rgba(52, 152, 219, 0.3);
}

.alert-info::before {
    content: 'ℹ️';
    font-size: 1.5rem;
    margin-right: 10px;
}

.alert::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
}

.alert:hover::after {
    left: 100%;
}

/* === ANIMATIONS === */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.container {
    animation: slideIn 0.8s ease-out;
}

.table tbody tr {
    animation: slideIn 0.6s ease-out;
    animation-fill-mode: both;
}

.table tbody tr:nth-child(1) { animation-delay: 0.1s; }
.table tbody tr:nth-child(2) { animation-delay: 0.2s; }
.table tbody tr:nth-child(3) { animation-delay: 0.3s; }
.table tbody tr:nth-child(4) { animation-delay: 0.4s; }
.table tbody tr:nth-child(5) { animation-delay: 0.5s; }

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .container {
        padding: 20px;
        border-radius: 15px;
    }
    
    .container h2 {
        font-size: 2rem;
        margin-bottom: 30px;
    }
    
    .table {
        font-size: 0.9rem;
    }
    
    .table th, .table td {
        padding: 15px 10px;
    }
    
    .table thead tr {
        height: 50px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 10px;
    }
    
    .container {
        padding: 15px;
    }
    
    .container h2 {
        font-size: 1.8rem;
    }
    
    .table {
        font-size: 0.8rem;
    }
    
    .table th, .table td {
        padding: 10px 8px;
    }
    
    /* Tableau responsive - version mobile */
    .table, .table thead, .table tbody, .table th, .table td, .table tr {
        display: block;
    }
    
    .table thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
        height: auto;
    }
    
    .table tr {
        border: 1px solid #ddd;
        margin-bottom: 15px;
        border-radius: 10px;
        padding: 15px;
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .table td {
        border: none;
        position: relative;
        padding: 10px 10px 10px 35%;
        text-align: left;
    }
    
    .table td:before {
        content: attr(data-label) ": ";
        position: absolute;
        left: 10px;
        width: 30%;
        font-weight: bold;
        color: #2c3e50;
        text-align: left;
    }
}

/* === EFFETS DE SURVOL AVANCÉS === */
.table tbody tr:hover td:first-child {
    color: #667eea;
    font-size: 1.15rem;
    transition: all 0.3s ease;
}

.table tbody tr:hover td:nth-child(3) {
    color: #e74c3c;
    font-size: 1.15rem;
    font-weight: 800;
    transition: all 0.3s ease;
}

/* === SCROLL SMOOTH === */
html {
    scroll-behavior: smooth;
}

/* === SÉLECTION DE TEXTE === */
::selection {
    background: #667eea;
    color: white;
}

::-moz-selection {
    background: #667eea;
    color: white;
}
</style>
<body>
<div class="container">
    <h2>Types de prêt disponibles</h2>
    
    <?php if (!empty($types)): ?>
    
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Taux d'intérêt</th>
                    <th>Durée maximale</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?= htmlspecialchars($type['nom']) ?></td>
                        <td><?= htmlspecialchars($type['taux_interet']) ?>%</td>
                        <td><?= htmlspecialchars($type['duree_max']) ?> mois</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Aucun type de prêt disponible pour votre profil.</div>
    <?php endif; ?>
</div>
</body>
</html>
