<?php
// config/setup_db.php
require_once __DIR__ . '/database.php';

function initializeDatabase() {
    try {
        $db = Database::getConnection();
        
        // 1. Table des Utilisateurs
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'manager', 'transcriptor') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB;");

        // 2. Table des Mois de Production
        $db->exec("CREATE TABLE IF NOT EXISTS production_months (
            id INT AUTO_INCREMENT PRIMARY KEY,
            month_name VARCHAR(20) NOT NULL,
            year_int INT NOT NULL,
            slug VARCHAR(30) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_month_year (month_name, year_int)
        ) ENGINE=InnoDB;");

        // 3. Table des Journées de Production
        $db->exec("CREATE TABLE IF NOT EXISTS production_days (
            id INT AUTO_INCREMENT PRIMARY KEY,
            month_id INT NOT NULL,
            date_day DATE NOT NULL UNIQUE,
            FOREIGN KEY (month_id) REFERENCES production_months(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;");

        // 4. Table des Retranscriptions (Tâches)
        $db->exec("CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            day_id INT NOT NULL,
            channel VARCHAR(100) NOT NULL,
            show_name VARCHAR(100) NOT NULL,
            air_time TIME NOT NULL,
            title VARCHAR(255) NOT NULL,
            speaker VARCHAR(255) NULL,
            video_duration INT NULL,
            assigned_to INT NULL,
            status ENUM('todo', 'in_progress', 'done', 'validated') DEFAULT 'todo',
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            delivery_deadline DATETIME NULL,
            comment TEXT NULL,
            file_link VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (day_id) REFERENCES production_days(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB;");

        // 5. Table d'Historique des Modifications
        $db->exec("CREATE TABLE IF NOT EXISTS task_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task_id INT NOT NULL,
            user_id INT NOT NULL,
            field_modified VARCHAR(100) NOT NULL,
            old_value TEXT NULL,
            new_value TEXT NULL,
            modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB;");

        // 6. Insertion du compte administrateur si la table est vide
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
            $insert = $db->prepare("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@production.com', :pass, 'admin')");
            $insert->execute([':pass' => $adminPass]);
        }

        return true;
    } catch (Exception $e) {
        die("Erreur lors de la création des tables : " . $e->getMessage());
    }
}