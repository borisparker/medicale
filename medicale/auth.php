<?php
session_start();

// Fonction pour vérifier le rôle (optionnel)
function require_role($role) {
    if (
        !isset($_SESSION['user']) ||
        !is_array($_SESSION['user']) ||
        !isset($_SESSION['user']['role']) ||
        $_SESSION['user']['role'] !== $role
    ) {
        header('Location: login.php');
        exit();
    }
}

// Vérifie simplement la connexion
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
// Pour une vérification de rôle stricte sur une page, ajouter :
// require_role('patient'); // ou 'docteur', 'admin' 