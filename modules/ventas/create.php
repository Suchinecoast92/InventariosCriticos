<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener lotes con información de cosecha y siembra
$query = "SELECT l.idLote, l.iKilos, l.dFechaEmpaque, c.idCosecha, s.vNombre, s.vZona
          FROM lotes l
          INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
          INNER JOIN siembras s ON c.idSiembra = s.idSiembra
          ORDER BY l.dFechaEmpaque DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$lotes = $stmt->fetchAll();

// Obtener clientes FEDERADOS
$query = "SELECT idCliente, vNombre, vRFC FROM clientes_federados ORDER BY vNombre";
$stmt = $db->prepare($query);
$stmt->execute();
$clientes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idLote = $_POST['idLote'];
    $idCliente = $_POST['idCliente'];
    $dFechaVenta = $_POST['dFechaVenta'];
    $iKilos = $_POST['iKilos'];
    
    $query = "INSERT INTO ventas (idLote, idCliente, dFechaVenta, iKilos) 
              VALUES (:idLote, :idCliente, :dFechaVenta, :iKilos)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->bindParam(':idCliente', $idCliente);
    $stmt->bindParam(':dFechaVenta', $dFechaVenta);
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
            <div class="card-header bg-warning">
                <h4><i class="bi bi-plus-circle"></i> Nueva Venta</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-cloud-fill"></i> Los clientes se obtienen desde la tabla federada del servidor
                </div>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Lote</label>
                        <select class="form-select" name="idLote" required>
                            <option value="">Seleccionar lote...</option>
                            <?php foreach($lotes as $lote): ?>
                            <option value="<?php echo $lote['idLote']; ?>">
                                Lote #<?php echo $lote['idLote']; ?> - <?php echo htmlspecialchars($lote['vNombre']); ?> 
                                (<?php echo $lote['vZona']; ?>) - <?php echo $lote['iKilos']; ?> kg
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-cloud-fill text-info"></i> Cliente (Federado)</label>
                        <select class="form-select" name="idCliente" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php foreach($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['idCliente']; ?>">
                                <?php echo htmlspecialchars($cliente['vNombre']); ?> - <?php echo $cliente['vRFC']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Venta</label>
                        <input type="date" class="form-control" name="dFechaVenta" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kilos Vendidos</label>
                        <input type="number" step="0.01" class="form-control" name="iKilos" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
