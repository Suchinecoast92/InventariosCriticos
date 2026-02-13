<?php 
require_once '../../config/database_servidor.php';

$database = new DatabaseServidor();
$db = $database->getConnection();

$idCliente = isset($_GET['id']) ? $_GET['id'] : die('ID no especificado');

try {
    // Verificar si el cliente tiene ventas asociadas
    // Primero necesitamos conectar a la base local para verificar
    require_once '../../config/database.php';
    $databaseLocal = new Database();
    $dbLocal = $databaseLocal->getConnection();
    
    $queryCheck = "SELECT COUNT(*) as total FROM ventas WHERE idCliente = :idCliente";
    $stmtCheck = $dbLocal->prepare($queryCheck);
    $stmtCheck->bindParam(':idCliente', $idCliente);
    $stmtCheck->execute();
    $result = $stmtCheck->fetch();
    
    if ($result['total'] > 0) {
        echo "<script>alert('No se puede eliminar este cliente porque tiene " . $result['total'] . " venta(s) asociada(s)'); window.location.href='index.php';</script>";
        exit;
    }
    
    // Si no tiene ventas, eliminar del servidor
    $query = "DELETE FROM clientes WHERE idCliente = :idCliente";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idCliente', $idCliente);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cliente eliminado exitosamente'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el cliente'); window.location.href='index.php';</script>";
    }
} catch(Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='index.php';</script>";
}
?>
