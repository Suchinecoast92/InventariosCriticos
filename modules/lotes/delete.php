<?php 
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$idLote = isset($_GET['id']) ? $_GET['id'] : die('ID no especificado');

try {
    // Verificar si el lote tiene ventas asociadas
    $queryVentas = "SELECT COUNT(*) as total FROM ventas WHERE idLote = :idLote";
    $stmtVentas = $db->prepare($queryVentas);
    $stmtVentas->bindParam(':idLote', $idLote);
    $stmtVentas->execute();
    $ventas = $stmtVentas->fetch();
    
    // Verificar si el lote tiene mermas asociadas
    $queryMermas = "SELECT COUNT(*) as total FROM mermas WHERE idLote = :idLote";
    $stmtMermas = $db->prepare($queryMermas);
    $stmtMermas->bindParam(':idLote', $idLote);
    $stmtMermas->execute();
    $mermas = $stmtMermas->fetch();
    
    if ($ventas['total'] > 0 || $mermas['total'] > 0) {
        $mensaje = "No se puede eliminar este lote porque tiene:\\n";
        if ($ventas['total'] > 0) {
            $mensaje .= "- " . $ventas['total'] . " venta(s) asociada(s)\\n";
        }
        if ($mermas['total'] > 0) {
            $mensaje .= "- " . $mermas['total'] . " merma(s) asociada(s)\\n";
        }
        echo "<script>alert('" . $mensaje . "'); window.location.href='index.php';</script>";
        exit;
    }
    
    // Si no tiene dependencias, eliminar
    $query = "DELETE FROM lotes WHERE idLote = :idLote";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    
    if ($stmt->execute()) {
        echo "<script>alert('Lote #" . $idLote . " eliminado exitosamente'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el lote'); window.location.href='index.php';</script>";
    }
} catch(Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='index.php';</script>";
}
?>
