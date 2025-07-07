<?php
if (isset($page) && $page != null) {
    include __DIR__ . '/../' . $page . '.php';
} else {
}
?>