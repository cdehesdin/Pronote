<?php
require_once __DIR__ . '/src/php/db.php';

$login = 'eleve_demo';
$newPassword = 'eleve_demo';

// Création du hash sécurisé
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

// Mise à jour dans la base
$stmt = $link->prepare("UPDATE Utilisateur SET mdp = ? WHERE login = ?");
$stmt->bind_param("ss", $hash, $login);
$stmt->execute();

echo "Mot de passe réinitialisé pour $login";
?>