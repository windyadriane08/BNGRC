<?php
/**
 * Script d'initialisation de la base de données BNGRC
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'bngrc';
$socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

try {
    // Connexion via socket XAMPP
    $dsn = file_exists($socket) 
        ? "mysql:unix_socket=$socket;charset=utf8mb4"
        : "mysql:host=$host;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connexion à MySQL réussie\n";
    
    $sqlFile = __DIR__ . '/database/1.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Le fichier SQL n'existe pas: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Supprimer la base si elle existe et la recréer
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    echo "✓ Ancienne base supprimée (si existante)\n";
    
    $pdo->exec($sql);
    
    echo "✓ Base de données créée avec succès\n";
    
    $pdo->exec("USE $dbname");
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Tables créées:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    
    echo "\n✅ Initialisation terminée!\n";
    echo "🌐 Accédez à l'application: http://localhost:8000\n\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "\n⚠️  Assurez-vous que MySQL est installé et démarré.\n";
    echo "   brew install mysql && brew services start mysql\n\n";
    exit(1);
}
