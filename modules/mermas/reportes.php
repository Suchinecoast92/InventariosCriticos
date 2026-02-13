<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Reporte 1: Mermas por Tipo
$query1 = "SELECT 
    m.vTipoMerma AS TipoMerma,
    COUNT(m.idMerma) AS NumeroIncidentes,
    SUM(m.iCantidad) AS TotalMerma,
    AVG(m.iCantidad) AS PromedioMerma
FROM mermas m
GROUP BY m.vTipoMerma
ORDER BY TotalMerma DESC";
$stmt1 = $db->prepare($query1);
$stmt1->execute();
$reporte1 = $stmt1->fetchAll();

// Reporte 2: Mermas por Zona
$query2 = "SELECT 
    s.vZona AS Zona,
    COUNT(m.idMerma) AS NumeroIncidentes,
    SUM(m.iCantidad) AS TotalMerma,
    AVG(m.iCantidad) AS PromedioMerma
FROM mermas m
INNER JOIN lotes l ON m.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
GROUP BY s.vZona
ORDER BY TotalMerma DESC";
$stmt2 = $db->prepare($query2);
$stmt2->execute();
$reporte2 = $stmt2->fetchAll();

// Reporte 3: Mermas Mensuales con Detalle
$query3 = "SELECT 
    DATE_FORMAT(m.dFecha, '%Y-%m') AS Mes,
    s.vZona,
    m.vTipoMerma,
    COUNT(m.idMerma) AS Incidentes,
    SUM(m.iCantidad) AS TotalMerma
FROM mermas m
INNER JOIN lotes l ON m.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
GROUP BY Mes, s.vZona, m.vTipoMerma
ORDER BY Mes DESC, TotalMerma DESC";
$stmt3 = $db->prepare($query3);
$stmt3->execute();
$reporte3 = $stmt3->fetchAll();

// Reporte 4: Trazabilidad de Mermas (detalle completo)
$query4 = "SELECT 
    m.idMerma,
    m.dFecha,
    m.vTipoMerma,
    m.iCantidad,
    l.idLote,
    c.idCosecha,
    s.idSiembra,
    s.vNombre AS NombreSiembra,
    s.vZona
FROM mermas m
INNER JOIN lotes l ON m.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
ORDER BY m.dFecha DESC";
$stmt4 = $db->prepare($query4);
$stmt4->execute();
$reporte4 = $stmt4->fetchAll();

// Reporte 5: Resumen Total
$query5 = "SELECT 
    COUNT(m.idMerma) AS TotalIncidentes,
    SUM(m.iCantidad) AS TotalKilosMerma,
    AVG(m.iCantidad) AS PromedioMerma,
    MIN(m.iCantidad) AS MinimoMerma,
    MAX(m.iCantidad) AS MaximoMerma
FROM mermas m";
$stmt5 = $db->prepare($query5);
$stmt5->execute();
$resumen = $stmt5->fetch();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes de Mermas</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<!-- Resumen General -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5><i class="bi bi-exclamation-triangle"></i> Resumen General de Mermas</h5>
                <div class="row text-center mt-3">
                    <div class="col">
                        <h3><?php echo $resumen['TotalIncidentes']; ?></h3>
                        <p>Total Incidentes</p>
                    </div>
                    <div class="col">
                        <h3><?php echo number_format($resumen['TotalKilosMerma'], 2); ?> kg</h3>
                        <p>Total Kilos Merma</p>
                    </div>
                    <div class="col">
                        <h3><?php echo number_format($resumen['PromedioMerma'], 2); ?> kg</h3>
                        <p>Promedio</p>
                    </div>
                    <div class="col">
                        <h3><?php echo number_format($resumen['MaximoMerma'], 2); ?> kg</h3>
                        <p>Máximo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reporte 1: Por Tipo -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5><i class="bi bi-tags"></i> Mermas por Tipo</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tipo de Merma</th>
                        <th>Núm. Incidentes</th>
                        <th>Total Merma (kg)</th>
                        <th>Promedio (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte1 as $row): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['TipoMerma']); ?></strong></td>
                        <td><?php echo $row['NumeroIncidentes']; ?></td>
                        <td class="text-danger"><strong><?php echo number_format($row['TotalMerma'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioMerma'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 2: Por Zona -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5><i class="bi bi-geo-alt"></i> Mermas por Zona</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Núm. Incidentes</th>
                        <th>Total Merma (kg)</th>
                        <th>Promedio (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte2 as $row): ?>
                    <tr>
                        <td><span class="badge bg-warning"><?php echo $row['Zona']; ?></span></td>
                        <td><?php echo $row['NumeroIncidentes']; ?></td>
                        <td class="text-danger"><strong><?php echo number_format($row['TotalMerma'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioMerma'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 3: Mensuales -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="bi bi-calendar-month"></i> Mermas Mensuales por Zona y Tipo</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaMensuales" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Zona</th>
                        <th>Tipo Merma</th>
                        <th>Incidentes</th>
                        <th>Total Merma (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte3 as $row): ?>
                    <tr>
                        <td><?php echo $row['Mes']; ?></td>
                        <td><?php echo $row['vZona']; ?></td>
                        <td><?php echo htmlspecialchars($row['vTipoMerma']); ?></td>
                        <td><?php echo $row['Incidentes']; ?></td>
                        <td class="text-danger"><?php echo number_format($row['TotalMerma'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 4: Trazabilidad Completa -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="bi bi-diagram-3"></i> Trazabilidad Completa de Mermas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaDetalle" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Lote</th>
                        <th>Cosecha</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte4 as $row): ?>
                    <tr>
                        <td><?php echo $row['idMerma']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['dFecha'])); ?></td>
                        <td><?php echo htmlspecialchars($row['vTipoMerma']); ?></td>
                        <td class="text-danger"><strong><?php echo number_format($row['iCantidad'], 2); ?> kg</strong></td>
                        <td><?php echo $row['idLote']; ?></td>
                        <td><?php echo $row['idCosecha']; ?></td>
                        <td><?php echo $row['idSiembra']; ?></td>
                        <td><?php echo $row['vZona']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaMensuales, #tablaDetalle').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
