<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$idLote = isset($_GET['idLote']) ? $_GET['idLote'] : null;

// Obtener todos los lotes para el selector
$query = "SELECT l.idLote, l.dFechaEmpaque, l.iKilos, c.idCosecha, s.vNombre, s.vZona
          FROM lotes l
          INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
          INNER JOIN siembras s ON c.idSiembra = s.idSiembra
          ORDER BY l.dFechaEmpaque DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$lotes = $stmt->fetchAll();

if ($idLote) {
    // Información del Lote
    $query = "SELECT l.*, c.dFecha AS fechaCosecha, c.iKilos AS kilosCosecha,
              s.idSiembra, s.vNombre AS nombreSiembra, s.dFecha AS fechaSiembra, s.vZona
              FROM lotes l
              INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
              INNER JOIN siembras s ON c.idSiembra = s.idSiembra
              WHERE l.idLote = :idLote";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->execute();
    $lote = $stmt->fetch();
    
    // Ventas del Lote con Clientes (FEDERADO)
    $query = "SELECT v.*, cf.vNombre AS nombreCliente, cf.vRFC, cf.vTelefono, cf.vDireccion
              FROM ventas v
              LEFT JOIN clientes_federados cf ON v.idCliente = cf.idCliente
              WHERE v.idLote = :idLote
              ORDER BY v.dFechaVenta";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->execute();
    $ventas = $stmt->fetchAll();
    
    // Mermas del Lote
    $query = "SELECT * FROM mermas WHERE idLote = :idLote ORDER BY dFecha";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->execute();
    $mermas = $stmt->fetchAll();
    
    // Calcular totales
    $totalVendido = array_sum(array_column($ventas, 'iKilos'));
    $totalMerma = array_sum(array_column($mermas, 'iCantidad'));
    $disponible = $lote['iKilos'] - $totalVendido - $totalMerma;
}
?>

<div class="row mb-3">
    <div class="col-md-12">
        <h2><i class="bi bi-diagram-3"></i> Trazabilidad por Lote</h2>
        <p class="text-muted">Seguimiento completo del recorrido de cada lote</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-10">
                    <select class="form-select" name="idLote" required>
                        <option value="">Seleccionar un lote para rastrear...</option>
                        <?php foreach($lotes as $l): ?>
                        <option value="<?php echo $l['idLote']; ?>" <?php if($idLote == $l['idLote']) echo 'selected'; ?>>
                            Lote #<?php echo $l['idLote']; ?> - <?php echo htmlspecialchars($l['vNombre']); ?> 
                            (<?php echo $l['vZona']; ?>) - Empacado: <?php echo date('d/m/Y', strtotime($l['dFechaEmpaque'])); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100"><i class="bi bi-search"></i> Rastrear</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($idLote && $lote): ?>

<!-- Resumen del Lote -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?php echo number_format($lote['iKilos'], 2); ?> kg</h4>
                <p class="mb-0">Kilos Iniciales</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?php echo number_format($totalVendido, 2); ?> kg</h4>
                <p class="mb-0">Total Vendido</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-danger"><?php echo number_format($totalMerma, 2); ?> kg</h4>
                <p class="mb-0">Total Merma</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning"><?php echo number_format($disponible, 2); ?> kg</h4>
                <p class="mb-0">Disponible</p>
            </div>
        </div>
    </div>
</div>

<!-- Línea de Tiempo del Lote -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="bi bi-clock-history"></i> Línea de Tiempo - Lote #<?php echo $lote['idLote']; ?></h5>
    </div>
    <div class="card-body">
        <div class="timeline">
            <!-- Siembra -->
            <div class="alert alert-success mb-3">
                <h5><i class="bi bi-seed"></i> 1. SIEMBRA</h5>
                <p><strong>ID:</strong> <?php echo $lote['idSiembra']; ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($lote['nombreSiembra']); ?></p>
                <p><strong>Zona:</strong> <span class="badge bg-success"><?php echo $lote['vZona']; ?></span></p>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($lote['fechaSiembra'])); ?></p>
            </div>
            
            <!-- Cosecha -->
            <div class="alert alert-primary mb-3">
                <h5><i class="bi bi-basket"></i> 2. COSECHA</h5>
                <p><strong>ID:</strong> <?php echo $lote['idCosecha']; ?></p>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($lote['fechaCosecha'])); ?></p>
                <p><strong>Kilos Cosechados:</strong> <?php echo number_format($lote['kilosCosecha'], 2); ?> kg</p>
            </div>
            
            <!-- Lote/Empaque -->
            <div class="alert alert-warning mb-3">
                <h5><i class="bi bi-box-seam"></i> 3. EMPAQUE (LOTE)</h5>
                <p><strong>ID Lote:</strong> <?php echo $lote['idLote']; ?></p>
                <p><strong>Fecha Empaque:</strong> <?php echo date('d/m/Y', strtotime($lote['dFechaEmpaque'])); ?></p>
                <p><strong>Kilos en Lote:</strong> <?php echo number_format($lote['iKilos'], 2); ?> kg</p>
            </div>
        </div>
    </div>
</div>

<!-- Ventas del Lote -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="bi bi-cart"></i> Ventas Realizadas (<?php echo count($ventas); ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (count($ventas) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Teléfono</th>
                        <th>Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ventas as $venta): ?>
                    <tr>
                        <td><?php echo $venta['idVenta']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($venta['dFechaVenta'])); ?></td>
                        <td><i class="bi bi-cloud-fill text-info"></i> <?php echo htmlspecialchars($venta['nombreCliente'] ?: 'N/A'); ?></td>
                        <td><?php echo $venta['vRFC'] ?: '-'; ?></td>
                        <td><?php echo $venta['vTelefono'] ?: '-'; ?></td>
                        <td><strong><?php echo number_format($venta['iKilos'], 2); ?> kg</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-success">
                        <td colspan="5" class="text-end"><strong>TOTAL VENDIDO:</strong></td>
                        <td><strong><?php echo number_format($totalVendido, 2); ?> kg</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted">No se han registrado ventas para este lote.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Mermas del Lote -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5><i class="bi bi-trash"></i> Mermas Registradas (<?php echo count($mermas); ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (count($mermas) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Merma</th>
                        <th>Fecha</th>
                        <th>Tipo de Merma</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mermas as $merma): ?>
                    <tr>
                        <td><?php echo $merma['idMerma']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($merma['dFecha'])); ?></td>
                        <td><?php echo htmlspecialchars($merma['vTipoMerma']); ?></td>
                        <td><strong class="text-danger"><?php echo number_format($merma['iCantidad'], 2); ?> kg</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-danger">
                        <td colspan="3" class="text-end"><strong>TOTAL MERMA:</strong></td>
                        <td><strong class="text-danger"><?php echo number_format($totalMerma, 2); ?> kg</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted">No se han registrado mermas para este lote.</p>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($idLote): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> No se encontró información para el lote especificado.
</div>
<?php else: ?>
<div class="alert alert-info text-center">
    <i class="bi bi-info-circle"></i> Seleccione un lote para ver su trazabilidad completa
</div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
