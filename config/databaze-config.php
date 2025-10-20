<?php
// Databázová konfigurace pro Sabčino zázračné cukrářství

// ==================================================
// DATABÁZOVÉ PŘIPOJENÍ
// ==================================================

// Databázové nastavení pro XAMPP (lokální vývoj)
define('DB_HOST', 'localhost');
define('DB_NAME', 'cukrarstvi_sabca');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP má výchozí prázdné heslo
define('DB_CHARSET', 'utf8mb4');

// Pro produkční server změňte tyto hodnoty:
// define('DB_HOST', 'your-server.com');
// define('DB_NAME', 'your_database');
// define('DB_USER', 'your_username');
// define('DB_PASS', 'your_secure_password');

// ==================================================
// NASTAVENÍ UPLOADŮ
// ==================================================

// Složky pro uploady - absolutní cesty od document root
$base_path = dirname(__DIR__); // Získáme cestu o úroveň výš (hlavní složku projektu)
define('UPLOAD_DIR', $base_path . '/uploads');
define('UPLOAD_RECEPTY_DIR', UPLOAD_DIR . '/recepty');
define('UPLOAD_GALERIE_DIR', UPLOAD_DIR . '/galerie');
define('UPLOAD_THUMBNAILS_DIR', UPLOAD_DIR . '/thumbnails');

// Pro web odkazy (relativní od document root)
define('WEB_UPLOAD_DIR', 'uploads');
define('WEB_UPLOAD_RECEPTY_DIR', WEB_UPLOAD_DIR . '/recepty');
define('WEB_UPLOAD_GALERIE_DIR', WEB_UPLOAD_DIR . '/galerie');
define('WEB_UPLOAD_THUMBNAILS_DIR', WEB_UPLOAD_DIR . '/thumbnails');

// Povolené typy souborů
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Velikosti thumbnailů
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);

// ==================================================
// BEZPEČNOSTNÍ NASTAVENÍ
// ==================================================

// Délka session pro admin (v sekundách)
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 hodina

// Počet neúspěšných pokusů o přihlášení
define('MAX_LOGIN_ATTEMPTS', 5);

// ==================================================
// GLOBÁLNÍ DATABÁZOVÁ TŘÍDA
// ==================================================

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Chyba připojení k databázi: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params);
    }
    
    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $whereParams);
    }
}

// ==================================================
// POMOCNÉ FUNKCE
// ==================================================

/**
 * Vytvoří potřebné složky pro uploady
 */
function createUploadDirectories() {
    $dirs = [
        UPLOAD_DIR,
        UPLOAD_RECEPTY_DIR,
        UPLOAD_GALERIE_DIR,
        UPLOAD_THUMBNAILS_DIR
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

/**
 * Ověří, zda je soubor povolený obrázek
 */
function isValidImage($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_IMAGE_TYPES);
}

/**
 * Vygeneruje bezpečný název souboru
 */
function generateSafeFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);
    
    // Odstranění diakritiky a nebezpečných znaků
    $basename = iconv('UTF-8', 'ASCII//TRANSLIT', $basename);
    $basename = preg_replace('/[^a-zA-Z0-9\-_]/', '', $basename);
    $basename = substr($basename, 0, 50); // Omezení délky
    
    return $basename . '_' . time() . '.' . $extension;
}

/**
 * Vytvoří thumbnail obrázku
 */
function createThumbnail($sourcePath, $targetPath, $width = THUMBNAIL_WIDTH, $height = THUMBNAIL_HEIGHT) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $sourceType = $imageInfo[2];
    
    // Vytvoření source obrázku
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    // Výpočet nových rozměrů (zachování poměru stran)
    $ratio = min($width / $sourceWidth, $height / $sourceHeight);
    $newWidth = (int)round($sourceWidth * $ratio);
    $newHeight = (int)round($sourceHeight * $ratio);
    
    // Vytvoření thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Zachování průhlednosti pro PNG
    if ($sourceType == IMAGETYPE_PNG) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
    }
    
    imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    
    // Uložení thumbnail
    $success = false;
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($thumbnail, $targetPath, 85);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($thumbnail, $targetPath);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($thumbnail, $targetPath);
            break;
    }
    
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return $success;
}

// ==================================================
// INICIALIZACE
// ==================================================

// Vytvoření složek při načtení
// Utilitní funkce pro formátování velikosti souborů
function formatujVelikostSouboru($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Inicializace
createUploadDirectories();

// Nastavení error reportingu pro development
if (DB_HOST === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

?>