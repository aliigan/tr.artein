<?php
/**
 * BuildTech CMS - Database Configuration
 * Veritabanı bağlantı ayarları
 */

// NOT: Sabitleri sadece tanımlı değilse tanımla (production'da override edilebilir)
if (!defined('DB_HOST')) { define('DB_HOST', 'localhost'); }
if (!defined('DB_NAME')) { define('DB_NAME', 'artein_db'); }
if (!defined('DB_USER')) { define('DB_USER', 'artein_user'); }
if (!defined('DB_PASS')) { define('DB_PASS', 'arteinHK24!'); }
if (!defined('DB_CHARSET')) { define('DB_CHARSET', 'utf8mb4'); }

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $pdo;
    private $lastErrorMessage = '';
    
    public function getConnection() {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            $this->lastErrorMessage = $exception->getMessage();
            error_log("Database connection error: " . $exception->getMessage());
            // Bağlantı hatasında uygulamayı öldürme; üst katmanlar hatayı ele alacak
            return null;
        }
        
        return $this->pdo;
    }
    
    public function fetchOne($sql, $params = []) {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) { return false; }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch(PDOException $e) {
            $this->lastErrorMessage = $e->getMessage();
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) { return []; }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            $this->lastErrorMessage = $e->getMessage();
            error_log("Database query error: " . $e->getMessage());
            return [];
        }
    }
    
    public function execute($sql, $params = []) {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) { return false; }
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute($params);
            if (!$ok) {
                $info = $stmt->errorInfo();
                $this->lastErrorMessage = is_array($info) ? implode(' | ', $info) : 'Unknown statement error';
            }
            return $ok;
        } catch(PDOException $e) {
            $this->lastErrorMessage = $e->getMessage();
            error_log("Database execute error: " . $e->getMessage());
            return false;
        }
    }
    
    public function lastInsertId() {
        $pdo = $this->getConnection();
        return $pdo ? $pdo->lastInsertId() : 0;
    }

    public function getLastErrorMessage(): string {
        return (string)$this->lastErrorMessage;
    }
}

$database = new Database();
$pdo = $database->getConnection();
?>
