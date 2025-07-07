<?php
try {
    $base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    header('Location: ' . $base_url. '/ws/auth/login');
    exit();
} catch (Exception $e) {
    // En cas d'erreur, afficher un message
    echo "Erreur de redirection : " . $e->getMessage();
}
?>