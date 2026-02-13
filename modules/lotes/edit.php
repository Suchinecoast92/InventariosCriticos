<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$idLote = isset($_GET['id']) ? $_GET['id'] : die('ID no especificado');

// Obtener datos del lote
$query = "SELECT l.*, c.iKilos as kilosCosecha, s.vNombre as nombreSiembra, s.vZona
          FROM lotes l
          INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
          INNER JOIN siembras s ON c.idSiembra = s.idSiembra
          WHERE l.idLote = :idLote";
$stmt = $db->prepare($query);
$stmt->bindParam(':idLote', $idLote);
$stmt->execute();
$lote = $stmt->fetch();

if (!$lote) {
    die('Lote no encontrado');
}

// Calcular kilos usados en otros lotes de la misma cosecha
$queryOtrosLotes = "SELECT COALESCE(SUM(iKilos), 0) as kilosOtrosLotes 
                    FROM lotes 
                    WHERE idCosecha = :idCosecha AND idLote != :idLote";
$stmtOtros = $db->prepare($queryOtrosLotes);
$stmtOtros->bindParam(':idCosecha', $lote['idCosecha']);
$stmtOtros->bindParam(':idLote', $idLote);
$stmtOtros->execute();
$otros = $stmtOtros->fetch();
$disponibleTotal = $lote['kilosCosecha'] - $otros['kilosOtrosLotes'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $iKilos = $_POST['iKilos'];
    $dFechaEmpaque = $_POST['dFechaEmpaque'];
    
    // Verificar que no exceda lo disponible
    if ($iKilos > $disponibleTotal) {
        echo "<script>alert('Error: Solo hay " . number_format($disponibleTotal, 2) . " kg disponibles');</script>";
    } else {
        $query = "UPDATE lotes SET 
                  iKilos = :iKilos, 
                  dFechaEmpaque = :dFechaEmpaque 
                  WHERE idLote = :idLote";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':iKilos', $iKilos);
        $stmt->bindParam(':dFechaEmpaque', $dFechaEmpaque);
        $stmt->bindParam(':idLote', $idLote);
        
        if ($stmt->execute()) {
            echo "<script>alert('Lote actualizado exitosamente'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el lote');</script>";
        }
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-pencil-square"></i> Editar Lote #<?php echo $lote['idLote']; ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="alert alert-info">
    <h6><i class="bi bi-info-circle"></i> Información de la Cosecha</h6>
    <p class="mb-1"><strong>Cosecha:</strong> #<?php echo $lote['idCosecha']; ?> - <?php echo htmlspecialchars($lote['nombreSiembra']); ?></p>
    <p class="mb-1"><strong>Zona:</strong> <?php echo $lote['vZona']; ?></p>
    <p class="mb-0"><strong>Disponible para este lote:</strong> <?php echo number_format($disponibleTotal, 2); ?> kg</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="iKilos" class="form-label">Kilos Empacados <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" max="<?php echo $disponibleTotal; ?>" 
                           class="form-control" id="iKilos" name="iKilos" required 
                           value="<?php echo $lote['iKilos']; ?>">
                    <small class="text-muted">Máximo disponible: <?php echo number_format($disponibleTotal, 2); ?> kg</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="dFechaEmpaque" class="form-label">Fecha de Empaque <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="dFechaEmpaque" name="dFechaEmpaque" 
                           required value="<?php echo $lote['dFechaEmpaque']; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Lote
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
