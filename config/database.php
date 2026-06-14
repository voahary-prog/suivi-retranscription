<?php
// config/database.php

// Remplacer avec les informations réelles de votre écran Aiven
define('DB_HOST', 'db-suivi-retranscription-suivi-retranscription.g.aivencloud.com');
define('DB_PORT', '16108');
define('DB_NAME', 'defaultdb'); // Sur Aiven, par défaut le nom est "defaultdb"
define('DB_USER', 'avnadmin');
define('DB_PASS', 'VOTRE_MOT_DE_PASSE_A_COPIER_SUR_AIVEN'); // Cliquez sur le petit œil sur Aiven pour le voir et le copier

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            try {
                // Connexion sécurisée avec le port spécifique fourni par Aiven
                self::$instance = new PDO(
                    "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur critique de connexion à la base de données en ligne : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}