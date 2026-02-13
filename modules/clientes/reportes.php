<?php 
require_once '../../config/database_servidor.php';
require_once '../../config/database.php';
include '../../includes/header.php';

$databaseServidor = new DatabaseServidor();
$dbServidor = $databaseServidor->getConnection();

$databaseLocal = new Database();
$dbLocal = $databaseLocal->getConnection();

// Reporte 1: Clientes con más compras
$query1 = "SELECT 
    cf.idCliente,
    cf.vNombre AS Cliente,
    cf.vRFC,
    cf.vTelefono,
    cf.vDireccion,
    COUNT(v.idVenta) AS NumeroCompras,
    SUM(v.iKilos) AS TotalKilos,
    AVG(v.iKilos) AS PromedioKilos
FROM clientes_federados cf
LEFT JOIN ventas v ON cf.idCliente = v.idCliente
GROUP BY cf.idCliente
ORDER BY NumeroCompras DESC, TotalKilos DESC";
$stmt1 = $dbLocal->prepare($query1);
$stmt1->execute();
$reporte1 = $stmt1->fetchAll();

// Reporte 2: Clientes sin compras
$query2 = "SELECT 
    c.idCliente,
    c.vNombre AS Cliente,
    c.vRFC,
    c.vTelefono,
    c.vDireccion
FROM clientes c
WHERE c.idCliente NOT IN (SELECT DISTINCT idCliente FROM sistema_limon_l.ventas)
ORDER BY c.vNombre";
$stmt2 = $dbServidor->prepare($query2);
$stmt2->execute();
$reporte2 = $stmt2->fetchAll();

// Reporte 3: Estadísticas generales
$query3 = "SELECT 
    COUNT(*) AS TotalClientes,
    COUNT(DISTINCT CASE WHEN v.idCliente IS NOT NULL THEN c.idCliente END) AS ClientesActivos,
    COUNT(DISTINCT CASE WHEN v.idCliente IS NULL THEN c.idCliente END) AS ClientesSinCompras
FROM clientes c
LEFT JOIN sistema_limon_l.ventas v ON c.idCliente = v.idCliente";
$stmt3 = $dbServidor->prepare($query3);
$stmt3->execute();
$stats = $stmt3->fetch();

// Reporte 4: Clientes por zona de compra
$query4 = "SELECT 
    cf.idCliente,
    cf.vNombre AS Cliente,
    s.vZona,
    COUNT(v.idVenta) AS NumeroCompras,
    SUM(v.iKilos) AS TotalKilos
FROM clientes_federados cf
INNER JOIN ventas v ON cf.idCliente = v.idCliente
INNER JOIN lotes l ON v.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
GROUP BY cf.idCliente, s.vZona
ORDER BY cf.vNombre, TotalKilos DESC";
$stmt4 = $dbLocal->prepare($query4);
$stmt4->execute();
$reporte4 = $stmt4->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes de Clientes</h2>
        <small class="text-muted"><i class="bi bi-cloud-fill"></i> Datos del Servidor</small>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<!-- Estadísticas Generales -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $stats['TotalClientes']; ?></h3>
                <p class="mb-0">Total Clientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-success"><?php echo $stats['ClientesActivos']; ?></h3>
                <p class="mb-0">Clientes Activos</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $stats['ClientesSinCompras']; ?></h3>
                <p class="mb-0">Sin Compras</p>
            </div>
        </div>
    </div>
</div>

<!-- Reporte 1: Clientes con más compras -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="bi bi-trophy"></i> Top Clientes por Compras</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Teléfono</th>
                        <th>Núm. Compras</th>
                        <th>Total Kilos</th>
                        <th>Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte1 as $row): ?>
                    <tr>
                        <td><?php echo $row['idCliente']; ?></td>
                        <td><?php echo htmlspecialchars($row['Cliente']); ?></td>
                        <td><?php echo htmlspecialchars($row['vRFC'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['vTelefono'] ?? '-'); ?></td>
                        <td><span class="badge bg-primary"><?php echo $row['NumeroCompras']; ?></span></td>
                        <td><strong><?php echo number_format($row['TotalKilos'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioKilos'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 2: Clientes sin compras -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5><i class="bi bi-exclamation-triangle"></i> Clientes sin Compras</h5>
    </div>
    <div class="card-body">
        <?php if (count($reporte2) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte2 as $row): ?>
                    <tr>
                        <td><?php echo $row['idCliente']; ?></td>
                        <td><?php echo htmlspecialchars($row['Cliente']); ?></td>
                        <td><?php echo htmlspecialchars($row['vRFC'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['vTelefono'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['vDireccion'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted">Todos los clientes tienen al menos una compra.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Reporte 4: Clientes por zona -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="bi bi-geo-alt"></i> Compras de Clientes por Zona</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaZonas" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Zona</th>
                        <th>Núm. Compras</th>
                        <th>Total Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte4 as $row): ?>
                    <tr>
                        <td><?php echo $row['idCliente']; ?></td>
                        <td><?php echo htmlspecialchars($row['Cliente']); ?></td>
                        <td><span class="badge bg-success"><?php echo $row['vZona']; ?></span></td>
                        <td><?php echo $row['NumeroCompras']; ?></td>
                        <td><strong><?php echo number_format($row['TotalKilos'], 2); ?> kg</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaZonas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
