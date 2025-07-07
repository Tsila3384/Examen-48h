<?php
// Pont vers l'application principale
try {
    // Redirection vers le workspace
    header('Location: /Examen-48h/tp-flightphp-crud/ws');
    exit();
} catch (Exception $e) {
    // En cas d'erreur, afficher un message
    echo "Erreur de redirection : " . $e->getMessage();
}
?>