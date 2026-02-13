<?php 
require_once '../../config/database_servidor.php';
include '../../includes/header.php';

$database = new DatabaseServidor();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vNombre = $_POST['vNombre'];
    $vRFC = $_POST['vRFC'];
    $vTelefono = $_POST['vTelefono'];
    $vDireccion = $_POST['vDireccion'];
    
    $query = "INSERT INTO clientes (vNombre, vRFC, vTelefono, vDireccion) 
              VALUES (:vNombre, :vRFC, :vTelefono, :vDireccion)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':vNombre', $vNombre);
    $stmt->bindParam(':vRFC', $vRFC);
    $stmt->bindParam(':vTelefono', $vTelefono);
    $stmt->bindParam(':vDireccion', $vDireccion);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cliente creado exitosamente'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error al crear el cliente');</script>";
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-person-plus"></i> Nuevo Cliente</h2>
        <small class="text-muted"><i class="bi bi-cloud-fill"></i> Servidor Remoto</small>
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
                    <input type="text" class="form-control" id="vNombre" name="vNombre" required maxlength="120">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="vRFC" class="form-label">RFC</label>
                    <input type="text" class="form-control" id="vRFC" name="vRFC" maxlength="20" 
                           placeholder="Ej: ABC123456XYZ">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="vTelefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="vTelefono" name="vTelefono" maxlength="20"
                           placeholder="Ej: 8123456789">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="vDireccion" class="form-label">Dirección</label>
                    <textarea class="form-control" id="vDireccion" name="vDireccion" rows="3" 
                              maxlength="200" placeholder="Dirección completa del cliente"></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Cliente
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
