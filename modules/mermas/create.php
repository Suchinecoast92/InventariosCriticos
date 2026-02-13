<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener lotes disponibles
$query = "SELECT l.idLote, l.iKilos, l.dFechaEmpaque, c.idCosecha, s.vNombre, s.vZona
          FROM lotes l
          INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
          INNER JOIN siembras s ON c.idSiembra = s.idSiembra
          ORDER BY l.dFechaEmpaque DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$lotes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idLote = $_POST['idLote'];
    $iCantidad = $_POST['iCantidad'];
    $vTipoMerma = $_POST['vTipoMerma'];
    $dFecha = $_POST['dFecha'];
    
    $query = "INSERT INTO mermas (idLote, iCantidad, vTipoMerma, dFecha) 
              VALUES (:idLote, :iCantidad, :vTipoMerma, :dFecha)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->bindParam(':iCantidad', $iCantidad);
    $stmt->bindParam(':vTipoMerma', $vTipoMerma);
    $stmt->bindParam(':dFecha', $dFecha);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=created");
        exit;
    }
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4><i class="bi bi-plus-circle"></i> Registrar Nueva Merma</h4>
            </div>
            <div class="card-body">
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
                        <label class="form-label">Tipo de Merma</label>
                        <select class="form-select" name="vTipoMerma" required>
                            <option value="">Seleccionar...</option>
                            <option value="Maduración excesiva">Maduración excesiva</option>
                            <option value="Daño físico">Daño físico</option>
                            <option value="Plagas">Plagas</option>
                            <option value="Enfermedades">Enfermedades</option>
                            <option value="Mal almacenamiento">Mal almacenamiento</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad de Merma (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="iCantidad" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="dFecha" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
