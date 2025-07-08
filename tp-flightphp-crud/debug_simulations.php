<?php
require_once 'tp-flightphp-crud/ws/db.php';

try {
    $db = getDB();
    
    echo "<h2>Debug des simulations</h2>";
    
    // 1. Vérifier la structure de la table simulations
    echo "<h3>1. Structure de la table simulations:</h3>";
    $stmt = $db->prepare("DESCRIBE simulations");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // 2. Compter les simulations
    echo "<h3>2. Nombre total de simulations:</h3>";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM simulations");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total: " . $count['count'] . " simulations<br>";
    
    // 3. Afficher toutes les simulations
    echo "<h3>3. Toutes les simulations:</h3>";
    $stmt = $db->prepare("SELECT * FROM simulations ORDER BY date_simulation DESC");
    $stmt->execute();
    $simulations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($simulations);
    echo "</pre>";
    
    // 4. Vérifier les utilisateurs et clients
    echo "<h3>4. Utilisateurs avec leurs clients:</h3>";
    $stmt = $db->prepare("
        SELECT u.id as user_id, u.username, u.role, c.id as client_id, c.nom 
        FROM users u 
        LEFT JOIN clients c ON u.id = c.user_id 
        WHERE u.role = 'client'
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($users);
    echo "</pre>";
    
    // 5. Test d'une simulation avec user_id = 2 (exemple)
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        echo "<h3>5. Simulations pour user_id = $user_id:</h3>";
        $stmt = $db->prepare("
            SELECT s.*, tp.nom as type_pret_nom
            FROM simulations s
            LEFT JOIN type_pret tp ON s.type_pret_id = tp.id
            WHERE s.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $userSimulations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($userSimulations);
        echo "</pre>";
    } else {
        echo "<h3>5. Test avec un user_id spécifique:</h3>";
        echo "<a href='?user_id=2'>Tester avec user_id=2</a><br>";
        echo "<a href='?user_id=3'>Tester avec user_id=3</a><br>";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>
