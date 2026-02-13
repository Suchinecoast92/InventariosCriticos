<?php
/**
 * Configuración de Conexión a Base de Datos
 * Sistema de Trazabilidad del Limón
 */

class Database {
    private $host = "localhost";
    private $db_name = "sistema_limon_l";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    public $conn;

    // Obtener conexión a la base de datos
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
