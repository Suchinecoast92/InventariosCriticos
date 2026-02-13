<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];

// Obtener lotes
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
    
    $query = "UPDATE ventas SET idLote = :idLote, idCliente = :idCliente, 
              dFechaVenta = :dFechaVenta, iKilos = :iKilos WHERE idVenta = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->bindParam(':idCliente', $idCliente);
    $stmt->bindParam(':dFechaVenta', $dFechaVenta);
    $stmt->bindParam(':iKilos', $iKilos);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=updated");
        exit;
    }
}

$query = "SELECT * FROM ventas WHERE idVenta = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$venta = $stmt->fetch();
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning">
                <h4><i class="bi bi-pencil"></i> Editar Venta</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Lote</label>
                        <select class="form-select" name="idLote" required>
                            <?php foreach($lotes as $lote): ?>
                            <option value="<?php echo $lote['idLote']; ?>" 
                                <?php if($lote['idLote'] == $venta['idLote']) echo 'selected'; ?>>
                                Lote #<?php echo $lote['idLote']; ?> - <?php echo htmlspecialchars($lote['vNombre']); ?> 
                                (<?php echo $lote['vZona']; ?>) - <?php echo $lote['iKilos']; ?> kg
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-cloud-fill text-info"></i> Cliente (Federado)</label>
                        <select class="form-select" name="idCliente" required>
                            <?php foreach($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['idCliente']; ?>" 
                                <?php if($cliente['idCliente'] == $venta['idCliente']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cliente['vNombre']); ?> - <?php echo $cliente['vRFC']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Venta</label>
                        <input type="date" class="form-control" name="dFechaVenta" 
                               value="<?php echo $venta['dFechaVenta']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kilos Vendidos</label>
                        <input type="number" step="0.01" class="form-control" name="iKilos" 
                               value="<?php echo $venta['iKilos']; ?>" required>
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
