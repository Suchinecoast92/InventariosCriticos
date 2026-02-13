<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener ventas con JOIN a lotes, cosechas, siembras y clientes FEDERADOS
$query = "SELECT 
    v.idVenta,
    v.dFechaVenta,
    v.iKilos,
    l.idLote,
    c.idCosecha,
    s.vNombre AS nombreSiembra,
    s.vZona,
    cf.idCliente,
    cf.vNombre AS nombreCliente,
    cf.vRFC
FROM ventas v
INNER JOIN lotes l ON v.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
LEFT JOIN clientes_federados cf ON v.idCliente = cf.idCliente
ORDER BY v.dFechaVenta DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$ventas = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-cart"></i> Gestión de Ventas</h2>
        <small class="text-muted"><i class="bi bi-cloud"></i> Integración con datos federados de clientes</small>
    </div>
    <div class="col-md-6 text-end">
        <a href="create.php" class="btn btn-warning"><i class="bi bi-plus-circle"></i> Nueva Venta</a>
        <a href="reportes.php" class="btn btn-info"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaVentas" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Lote</th>
                        <th>Zona Origen</th>
                        <th>Kilos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ventas as $venta): ?>
                    <tr>
                        <td><?php echo $venta['idVenta']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($venta['dFechaVenta'])); ?></td>
                        <td>
                            <i class="bi bi-cloud-fill text-info"></i> 
                            <?php echo htmlspecialchars($venta['nombreCliente'] ?: 'Cliente N/A'); ?>
                        </td>
                        <td><?php echo $venta['vRFC'] ?: '-'; ?></td>
                        <td><?php echo $venta['idLote']; ?></td>
                        <td><span class="badge bg-warning"><?php echo $venta['vZona']; ?></span></td>
                        <td><strong><?php echo number_format($venta['iKilos'], 2); ?> kg</strong></td>
                        <td>
                            <a href="edit.php?id=<?php echo $venta['idVenta']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $venta['idVenta']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar esta venta?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaVentas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
