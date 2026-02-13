<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vNombre = $_POST['vNombre'];
    $dFecha = $_POST['dFecha'];
    $vZona = $_POST['vZona'];
    
    $query = "INSERT INTO siembras (vNombre, dFecha, vZona) VALUES (:vNombre, :dFecha, :vZona)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':vNombre', $vNombre);
    $stmt->bindParam(':dFecha', $dFecha);
    $stmt->bindParam(':vZona', $vZona);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=created");
        exit;
    }
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4><i class="bi bi-plus-circle"></i> Nueva Siembra</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Siembra</label>
                        <input type="text" class="form-control" name="vNombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="dFecha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Zona</label>
                        <select class="form-select" name="vZona" required>
                            <option value="">Seleccionar...</option>
                            <option value="NORTE">NORTE</option>
                            <option value="SUR">SUR</option>
                            <option value="ESTE">ESTE</option>
                            <option value="OESTE">OESTE</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
