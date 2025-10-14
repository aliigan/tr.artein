<?php
/**
 * BuildTech CMS - Database Configuration
 * Veritabanı bağlantı ayarları
 */

// Geliştirme ortamı için ayarlar
// Canlı sunucuda bu değerleri değiştirin
define('DB_HOST', '94.138.202.230');
define('DB_NAME', 'artein_db');
define('DB_USER', 'artein_user');
define('DB_PASS', 'arteinHK24!');
define('DB_CHARSET', 'utf8mb4');

// Veritabanı bağlantısı sınıfı
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $pdo;
    
    public function getConnection() {
        $this->pdo = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            die("Veritabanı bağlantı hatası. Lütfen daha sonra tekrar deneyin.");
        }
        
        return $this->pdo;
    }
    
    // Tekli kayıt getirme
    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    // Çoklu kayıt getirme
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return [];
        }
    }
    
    // Veri ekleme/güncelleme/silme
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            error_log("Database execute error: " . $e->getMessage());
            return false;
        }
    }
    
    // Son eklenen kaydın ID'sini alma
    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }
}

// Global veritabanı nesnesi
$database = new Database();
$pdo = $database->getConnection();
?>
