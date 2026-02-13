<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener siembras para el dropdown
$query = "SELECT idSiembra, vNombre, vZona FROM siembras ORDER BY dFecha DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$siembras = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idSiembra = $_POST['idSiembra'];
    $dFecha = $_POST['dFecha'];
    $iKilos = $_POST['iKilos'];
    
    $query = "INSERT INTO cosechas (idSiembra, dFecha, iKilos) VALUES (:idSiembra, :dFecha, :iKilos)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idSiembra', $idSiembra);
    $stmt->bindParam(':dFecha', $dFecha);
    $stmt->bindParam(':iKilos', $iKilos);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=created");
        exit;
    }
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-plus-circle"></i> Nueva Cosecha</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Siembra</label>
                        <select class="form-select" name="idSiembra" required>
                            <option value="">Seleccionar siembra...</option>
                            <?php foreach($siembras as $siembra): ?>
                            <option value="<?php echo $siembra['idSiembra']; ?>">
                                <?php echo htmlspecialchars($siembra['vNombre']); ?> - <?php echo $siembra['vZona']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Cosecha</label>
                        <input type="date" class="form-control" name="dFecha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kilos Cosechados</label>
                        <input type="number" step="0.01" class="form-control" name="iKilos" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
