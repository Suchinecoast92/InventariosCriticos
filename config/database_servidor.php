<?php
/**
 * Configuración de Conexión a Base de Datos del Servidor
 * Sistema de Trazabilidad del Limón
 */

class DatabaseServidor {
    private $host = "10.20.41.160";
    private $db_name = "sistema_limon_s";
    private $username = "admin";
    private $password = "informatica";
    private $charset = "utf8mb4";
    public $conn;

    // Obtener conexión a la base de datos del servidor
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
            echo "Error de conexión al servidor: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
