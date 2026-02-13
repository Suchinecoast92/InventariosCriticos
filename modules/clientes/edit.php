<?php 
require_once '../../config/database_servidor.php';
include '../../includes/header.php';

$database = new DatabaseServidor();
$db = $database->getConnection();

$idCliente = isset($_GET['id']) ? $_GET['id'] : die('ID no especificado');

// Obtener datos del cliente
$query = "SELECT * FROM clientes WHERE idCliente = :idCliente";
$stmt = $db->prepare($query);
$stmt->bindParam(':idCliente', $idCliente);
$stmt->execute();
$cliente = $stmt->fetch();

if (!$cliente) {
    die('Cliente no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vNombre = $_POST['vNombre'];
    $vRFC = $_POST['vRFC'];
    $vTelefono = $_POST['vTelefono'];
    $vDireccion = $_POST['vDireccion'];
    
    $query = "UPDATE clientes SET 
              vNombre = :vNombre, 
              vRFC = :vRFC, 
              vTelefono = :vTelefono, 
              vDireccion = :vDireccion 
              WHERE idCliente = :idCliente";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':vNombre', $vNombre);
    $stmt->bindParam(':vRFC', $vRFC);
    $stmt->bindParam(':vTelefono', $vTelefono);
    $stmt->bindParam(':vDireccion', $vDireccion);
    $stmt->bindParam(':idCliente', $idCliente);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cliente actualizado exitosamente'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el cliente');</script>";
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-pencil-square"></i> Editar Cliente</h2>
        <small class="text-muted"><i class="bi bi-cloud-fill"></i> Servidor Remoto - ID: <?php echo $cliente['idCliente']; ?></small>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="vNombre" class="form-label">Nombre del Cliente <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="vNombre" name="vNombre" required maxlength="120"
                           value="<?php echo htmlspecialchars($cliente['vNombre']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="vRFC" class="form-label">RFC</label>
                    <input type="text" class="form-control" id="vRFC" name="vRFC" maxlength="20"
                           value="<?php echo htmlspecialchars($cliente['vRFC'] ?? ''); ?>"
                           placeholder="Ej: ABC123456XYZ">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="vTelefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="vTelefono" name="vTelefono" maxlength="20"
                           value="<?php echo htmlspecialchars($cliente['vTelefono'] ?? ''); ?>"
                           placeholder="Ej: 8123456789">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="vDireccion" class="form-label">Dirección</label>
                    <textarea class="form-control" id="vDireccion" name="vDireccion" rows="3" 
                              maxlength="200" placeholder="Dirección completa del cliente"><?php echo htmlspecialchars($cliente['vDireccion'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Cliente
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
