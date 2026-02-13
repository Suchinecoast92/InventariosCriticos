<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];

// Obtener siembras
$query = "SELECT idSiembra, vNombre, vZona FROM siembras ORDER BY dFecha DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$siembras = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idSiembra = $_POST['idSiembra'];
    $dFecha = $_POST['dFecha'];
    $iKilos = $_POST['iKilos'];
    
    $query = "UPDATE cosechas SET idSiembra = :idSiembra, dFecha = :dFecha, iKilos = :iKilos WHERE idCosecha = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idSiembra', $idSiembra);
    $stmt->bindParam(':dFecha', $dFecha);
    $stmt->bindParam(':iKilos', $iKilos);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=updated");
        exit;
    }
}

$query = "SELECT * FROM cosechas WHERE idCosecha = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$cosecha = $stmt->fetch();
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning">
                <h4><i class="bi bi-pencil"></i> Editar Cosecha</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Siembra</label>
                        <select class="form-select" name="idSiembra" required>
                            <?php foreach($siembras as $siembra): ?>
                            <option value="<?php echo $siembra['idSiembra']; ?>" 
                                <?php if($siembra['idSiembra'] == $cosecha['idSiembra']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($siembra['vNombre']); ?> - <?php echo $siembra['vZona']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Cosecha</label>
                        <input type="date" class="form-control" name="dFecha" value="<?php echo $cosecha['dFecha']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kilos Cosechados</label>
                        <input type="number" step="0.01" class="form-control" name="iKilos" value="<?php echo $cosecha['iKilos']; ?>" required>
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
