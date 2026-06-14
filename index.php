<?php
// index.php à la racine du projet

// 1. Initialiser la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Lancer l'initialisation de la base de données
require_once __DIR__ . '/config/setup_db.php';
initializeDatabase();

// 3. Message temporaire de test
echo "<div style='font-family: sans-serif; text-align: center; margin-top: 10%;'>";
echo "<h1 style='color: #10B981;'>✓ Base de données initialisée avec succès !</h1>";
echo "<p style='color: #4B5563;'>Vos tables sont créées sur Aiven et le compte <b>admin</b> (mot de passe: <i>admin123</i>) est prêt.</p>";
echo "<p style='color: #9CA3AF; font-size: 14px;'>Étape suivante : Déployer le code sur Render pour voir ce message en ligne.</p>";
echo "</div>";