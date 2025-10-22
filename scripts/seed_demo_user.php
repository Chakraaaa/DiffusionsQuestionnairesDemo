<?php
// scripts/seed_demo_user.php
// Exécuter: php scripts/seed_demo_user.php

use Appy\Src\Connexionbdd;
use Appy\Src\Str;

// Bootstrap minimal: autoload + .env (si présent)
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . DS . 'vendor' . DS . 'autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad();
}

echo "=== SEED UTILISATEUR DEMO ===\n";

// CONFIG — modifier si besoin
$demoEmail = 'demo@demo.com';
$demoPasswordPlain = 'demo123';
$demoLastname = 'Compte';
$demoFirstname = 'Démo';
$demoRole = 1; // 1 = ADMINISTRATEUR, adapter si besoin

try {
    // Connexion via couche existante
    $pdo = Connexionbdd::getInstance();

    // Génère le hash sécurisé (bcrypt / PASSWORD_DEFAULT)
    $hash = password_hash($demoPasswordPlain, PASSWORD_DEFAULT);

    // Vérifier si l'utilisateur existe (par email)
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$demoEmail]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($user) {
        // Met à jour le mot de passe et les infos de base
        $stmt = $pdo->prepare('UPDATE users SET password = ?, lastname = ?, firstname = ?, role = ?, confirmed_at = NOW() WHERE id = ?');
        $stmt->execute([$hash, $demoLastname, $demoFirstname, $demoRole, $user['id']]);
        echo "Utilisateur démo mis à jour (id = {$user['id']})\n";
    } else {
        // Crée un identifiant lisible (comme dans Entity\User)
        $characters = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $identifier = '';
        for ($i = 0; $i < 10; $i++) {
            $identifier .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Insert nouveau compte démo
        $stmt = $pdo->prepare('INSERT INTO users (lastname, firstname, identifier, email, password, role, confirmed_at, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([$demoLastname, $demoFirstname, $identifier, $demoEmail, $hash, $demoRole]);
        $newId = $pdo->lastInsertId();
        echo "Utilisateur démo créé (id = {$newId})\n";
    }

    echo "Email: {$demoEmail}\n";
    echo "Mot de passe: {$demoPasswordPlain}\n";
    echo "Note: le mot de passe stocké est haché (sécurisé).\n";
    echo "=== FIN SEED ===\n";
    exit(0);
} catch (Throwable $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}


