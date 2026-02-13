<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Reporte 1: Cosechas por Zona con Totales
$query1 = "SELECT 
    s.vZona AS Zona,
    COUNT(c.idCosecha) AS NumeroCosechas,
    SUM(c.iKilos) AS TotalKilos,
    AVG(c.iKilos) AS PromedioKilos,
    MIN(c.iKilos) AS MinimoKilos,
    MAX(c.iKilos) AS MaximoKilos
FROM siembras s
INNER JOIN cosechas c ON s.idSiembra = c.idSiembra
GROUP BY s.vZona
ORDER BY TotalKilos DESC";
$stmt1 = $db->prepare($query1);
$stmt1->execute();
$reporte1 = $stmt1->fetchAll();

// Reporte 2: Cosechas por Mes
$query2 = "SELECT 
    DATE_FORMAT(c.dFecha, '%Y-%m') AS Mes,
    COUNT(c.idCosecha) AS NumeroCosechas,
    SUM(c.iKilos) AS TotalKilos,
    AVG(c.iKilos) AS PromedioKilos
FROM cosechas c
GROUP BY Mes
ORDER BY Mes DESC";
$stmt2 = $db->prepare($query2);
$stmt2->execute();
$reporte2 = $stmt2->fetchAll();

// Reporte 3: Detalle de Cosechas con Siembras y Lotes
$query3 = "SELECT 
    c.idCosecha,
    c.dFecha AS FechaCosecha,
    c.iKilos AS KilosCosecha,
    s.vNombre AS NombreSiembra,
    s.vZona,
    COUNT(l.idLote) AS NumeroLotes,
    COALESCE(SUM(l.iKilos), 0) AS KilosEnLotes
FROM cosechas c
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
LEFT JOIN lotes l ON c.idCosecha = l.idCosecha
GROUP BY c.idCosecha
ORDER BY c.dFecha DESC";
$stmt3 = $db->prepare($query3);
$stmt3->execute();
$reporte3 = $stmt3->fetchAll();

// Reporte 4: Top Cosechas más Grandes
$query4 = "SELECT 
    c.idCosecha,
    s.vNombre AS NombreSiembra,
    s.vZona,
    c.dFecha,
    c.iKilos
FROM cosechas c
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
ORDER BY c.iKilos DESC
LIMIT 10";
$stmt4 = $db->prepare($query4);
$stmt4->execute();
$reporte4 = $stmt4->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes de Cosechas</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<!-- Reporte 1: Cosechas por Zona -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="bi bi-geo-alt"></i> Cosechas por Zona</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Núm. Cosechas</th>
                        <th>Total Kilos</th>
                        <th>Promedio</th>
                        <th>Mínimo</th>
                        <th>Máximo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte1 as $row): ?>
                    <tr>
                        <td><span class="badge bg-primary"><?php echo $row['Zona']; ?></span></td>
                        <td><?php echo $row['NumeroCosechas']; ?></td>
                        <td><strong><?php echo number_format($row['TotalKilos'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioKilos'], 2); ?> kg</td>
                        <td><?php echo number_format($row['MinimoKilos'], 2); ?> kg</td>
                        <td><?php echo number_format($row['MaximoKilos'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 2: Cosechas por Mes -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="bi bi-calendar-month"></i> Cosechas por Mes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Núm. Cosechas</th>
                        <th>Total Kilos</th>
                        <th>Promedio por Cosecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte2 as $row): ?>
                    <tr>
                        <td><?php echo $row['Mes']; ?></td>
                        <td><?php echo $row['NumeroCosechas']; ?></td>
                        <td><strong><?php echo number_format($row['TotalKilos'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($row['PromedioKilos'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 3: Detalle con Lotes -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5><i class="bi bi-boxes"></i> Cosechas y sus Lotes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaDetalle" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Kilos Cosecha</th>
                        <th>Núm. Lotes</th>
                        <th>Kilos en Lotes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte3 as $row): ?>
                    <tr>
                        <td><?php echo $row['idCosecha']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['FechaCosecha'])); ?></td>
                        <td><?php echo htmlspecialchars($row['NombreSiembra']); ?></td>
                        <td><?php echo $row['vZona']; ?></td>
                        <td><?php echo number_format($row['KilosCosecha'], 2); ?> kg</td>
                        <td><?php echo $row['NumeroLotes']; ?></td>
                        <td><?php echo number_format($row['KilosEnLotes'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 4: Top Cosechas -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="bi bi-trophy"></i> Top 10 Cosechas Más Grandes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Fecha</th>
                        <th>Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte4 as $row): ?>
                    <tr>
                        <td><?php echo $row['idCosecha']; ?></td>
                        <td><?php echo htmlspecialchars($row['NombreSiembra']); ?></td>
                        <td><?php echo $row['vZona']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['dFecha'])); ?></td>
                        <td><strong class="text-success"><?php echo number_format($row['iKilos'], 2); ?> kg</strong></td>
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
