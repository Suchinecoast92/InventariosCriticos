<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Reporte 1: Ventas por Cliente (FEDERADO - datos del servidor)
$query1 = "SELECT 
    cf.idCliente,
    cf.vNombre AS Cliente,
    cf.vTelefono,
    cf.vRFC,
    COUNT(v.idVenta) AS NumeroCompras,
    SUM(v.iKilos) AS TotalKilosComprados,
    AVG(v.iKilos) AS PromedioKilos
FROM clientes_federados cf
INNER JOIN ventas v ON cf.idCliente = v.idCliente
GROUP BY cf.idCliente
ORDER BY TotalKilosComprados DESC";
$stmt1 = $db->prepare($query1);
$stmt1->execute();
$reporte1 = $stmt1->fetchAll();

// Reporte 2: Ventas por Zona de Origen
$query2 = "SELECT 
    s.vZona AS Zona,
    COUNT(v.idVenta) AS NumeroVentas,
    SUM(v.iKilos) AS TotalKilosVendidos,
    COUNT(DISTINCT cf.idCliente) AS NumeroClientes
FROM ventas v
INNER JOIN lotes l ON v.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
LEFT JOIN clientes_federados cf ON v.idCliente = cf.idCliente
GROUP BY s.vZona
ORDER BY TotalKilosVendidos DESC";
$stmt2 = $db->prepare($query2);
$stmt2->execute();
$reporte2 = $stmt2->fetchAll();

// Reporte 3: Detalle de Ventas con Trazabilidad Completa
$query3 = "SELECT 
    v.idVenta,
    v.dFechaVenta,
    v.iKilos AS KilosVenta,
    cf.vNombre AS Cliente,
    cf.vRFC,
    l.idLote,
    c.idCosecha,
    s.idSiembra,
    s.vNombre AS NombreSiembra,
    s.vZona
FROM ventas v
INNER JOIN lotes l ON v.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
LEFT JOIN clientes_federados cf ON v.idCliente = cf.idCliente
ORDER BY v.dFechaVenta DESC";
$stmt3 = $db->prepare($query3);
$stmt3->execute();
$reporte3 = $stmt3->fetchAll();

// Reporte 4: Ventas por Mes
$query4 = "SELECT 
    DATE_FORMAT(v.dFechaVenta, '%Y-%m') AS Mes,
    COUNT(v.idVenta) AS NumeroVentas,
    SUM(v.iKilos) AS TotalKilos,
    AVG(v.iKilos) AS PromedioKilos,
    COUNT(DISTINCT v.idCliente) AS NumeroClientes
FROM ventas v
GROUP BY Mes
ORDER BY Mes DESC";
$stmt4 = $db->prepare($query4);
$stmt4->execute();
$reporte4 = $stmt4->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes de Ventas</h2>
        <small class="text-muted"><i class="bi bi-cloud-fill"></i> Con datos federados del servidor</small>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<!-- Reporte 1: Ventas por Cliente (FEDERADO) -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5><i class="bi bi-cloud-fill"></i> Ventas por Cliente (Datos Federados)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
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
                        <td><i class="bi bi-cloud-fill text-info"></i> <?php echo htmlspecialchars($row['Cliente']); ?></td>
                        <td><?php echo $row['vRFC']; ?></td>
                        <td><?php echo $row['vTelefono']; ?></td>
                        <td><?php echo $row['NumeroCompras']; ?></td>
                        <td><strong><?php echo number_format($row['TotalKilosComprados'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioKilos'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 2: Ventas por Zona -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="bi bi-geo-alt"></i> Ventas por Zona de Origen</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Núm. Ventas</th>
                        <th>Total Kilos Vendidos</th>
                        <th>Núm. Clientes Diferentes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte2 as $row): ?>
                    <tr>
                        <td><span class="badge bg-success"><?php echo $row['Zona']; ?></span></td>
                        <td><?php echo $row['NumeroVentas']; ?></td>
                        <td><strong><?php echo number_format($row['TotalKilosVendidos'], 2); ?> kg</strong></td>
                        <td><?php echo $row['NumeroClientes']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 3: Trazabilidad Completa de Ventas -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="bi bi-diagram-3"></i> Trazabilidad Completa de Ventas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaDetalle" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>Venta</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Lote</th>
                        <th>Cosecha</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte3 as $row): ?>
                    <tr>
                        <td><?php echo $row['idVenta']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['dFechaVenta'])); ?></td>
                        <td><?php echo htmlspecialchars($row['Cliente']); ?></td>
                        <td><?php echo $row['vRFC']; ?></td>
                        <td><?php echo $row['idLote']; ?></td>
                        <td><?php echo $row['idCosecha']; ?></td>
                        <td><?php echo $row['idSiembra']; ?></td>
                        <td><?php echo $row['vZona']; ?></td>
                        <td><?php echo number_format($row['KilosVenta'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 4: Ventas por Mes -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="bi bi-calendar-month"></i> Ventas por Mes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Núm. Ventas</th>
                        <th>Total Kilos</th>
                        <th>Promedio</th>
                        <th>Clientes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte4 as $row): ?>
                    <tr>
                        <td><?php echo $row['Mes']; ?></td>
                        <td><?php echo $row['NumeroVentas']; ?></td>
                        <td><strong><?php echo number_format($row['TotalKilos'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioKilos'], 2); ?> kg</td>
                        <td><?php echo $row['NumeroClientes']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaDetalle').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
