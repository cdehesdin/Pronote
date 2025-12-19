<?php
session_start();
require_once __DIR__ . '/src/php/config.php';

$title = "Relevé de notes";

if (!isset($_SESSION['login'])) {
    header('Location: ./index.php');
    exit;
}
?>

<?php ob_start(); ?>

<?php if (isset($_SESSION['login'])):?>



<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/src/php/layout.php';
?>