<?php
$title = "Accueil";
?>


<!-------------------------------------->
<!--        ADD STYLE AND SCRIPT     --->
<!-------------------------------------->
<?php ob_start(); ?>


<?php $style_script = ob_get_clean(); ?>


<!-------------------------------------->
<!--             ADD CONTENT         --->
<!-------------------------------------->
<?php ob_start(); ?>

e

<?php $content = ob_get_clean(); ?>


<?php require_once "./src/php/layout.php"; ?>