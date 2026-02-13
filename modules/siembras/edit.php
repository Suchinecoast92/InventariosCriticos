<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vNombre = $_POST['vNombre'];
    $dFecha = $_POST['dFecha'];
    $vZona = $_POST['vZona'];
    
    $query = "UPDATE siembras SET vNombre = :vNombre, dFecha = :dFecha, vZona = :vZona WHERE idSiembra = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':vNombre', $vNombre);
    $stmt->bindParam(':dFecha', $dFecha);
    $stmt->bindParam(':vZona', $vZona);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=updated");
        exit;
    }
}

$query = "SELECT * FROM siembras WHERE idSiembra = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$siembra = $stmt->fetch();
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning">
                <h4><i class="bi bi-pencil"></i> Editar Siembra</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Siembra</label>
                        <input type="text" class="form-control" name="vNombre" value="<?php echo htmlspecialchars($siembra['vNombre']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="dFecha" value="<?php echo $siembra['dFecha']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Zona</label>
                        <select class="form-select" name="vZona" required>
                            <option value="NORTE" <?php if($siembra['vZona']=='NORTE') echo 'selected'; ?>>NORTE</option>
                            <option value="SUR" <?php if($siembra['vZona']=='SUR') echo 'selected'; ?>>SUR</option>
                            <option value="ESTE" <?php if($siembra['vZona']=='ESTE') echo 'selected'; ?>>ESTE</option>
                            <option value="OESTE" <?php if($siembra['vZona']=='OESTE') echo 'selected'; ?>>OESTE</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
