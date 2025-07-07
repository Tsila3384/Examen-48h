<?php
try {
    $base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_url = rtrim($base_url, '/');
    $redirect = $base_url . '/ws/login';
    header('Location: ' . $redirect);
    exit();
} catch (Exception $e) {
    // En cas d'erreur, afficher un message
    echo "Erreur de redirection : " . $e->getMessage();
}
?>