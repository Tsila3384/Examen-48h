<?php
// Script de test pour vérifier la contrainte des fonds de l'établissement
require_once 'ws/db.php';
require_once 'ws/models/Pret.php';

echo "=== Test de la contrainte des fonds de l'établissement ===\n\n";

try {
    $db = getDB();
    
    // Vérifier les fonds actuels de l'établissement
    $stmt = $db->prepare("SELECT fonds_disponibles FROM etablissement WHERE id = 1");
    $stmt->execute();
    $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($etablissement) {
        $fondsDisponibles = floatval($etablissement['fonds_disponibles']);
        echo "Fonds disponibles actuels: " . number_format($fondsDisponibles, 2) . " €\n\n";
        
        // Test 1: Tentative de créer un prêt avec un montant raisonnable
        echo "Test 1: Demande de prêt de 10 000 €\n";
        $pretModel = new Pret();
        
        try {
            $pretId = $pretModel->insererPret(
                1, // client_id
                10000, // montant
                1, // type_pret_id
                date('Y-m-d'), // date_debut
                12, // duree
                1.5, // taux_assurance
                1 // delai_premier_remboursement
            );
            echo "✓ Prêt créé avec succès (ID: $pretId)\n\n";
        } catch (Exception $e) {
            echo "✗ Erreur: " . $e->getMessage() . "\n\n";
        }
        
        // Test 2: Tentative de créer un prêt avec un montant très élevé
        echo "Test 2: Demande de prêt de " . number_format($fondsDisponibles + 100000, 2) . " €\n";
        
        try {
            $pretId = $pretModel->insererPret(
                1, // client_id
                $fondsDisponibles + 100000, // montant supérieur aux fonds
                1, // type_pret_id
                date('Y-m-d'), // date_debut
                24, // duree
                2.0, // taux_assurance
                0 // delai_premier_remboursement
            );
            echo "✗ Prêt créé alors qu'il ne devrait pas (ID: $pretId)\n\n";
        } catch (Exception $e) {
            echo "✓ Exception attendue: " . $e->getMessage() . "\n\n";
        }
        
        // Vérifier à nouveau les fonds après les tests
        $stmt->execute();
        $etablissementApres = $stmt->fetch(PDO::FETCH_ASSOC);
        $fondsApres = floatval($etablissementApres['fonds_disponibles']);
        echo "Fonds disponibles après les tests: " . number_format($fondsApres, 2) . " €\n";
        
    } else {
        echo "Erreur: Établissement non trouvé\n";
    }
    
} catch (Exception $e) {
    echo "Erreur générale: " . $e->getMessage() . "\n";
}

echo "\n=== Fin des tests ===\n";
?>
