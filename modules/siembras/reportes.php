<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Reporte 1: Producción por Zona (JOIN con cosechas)
$query1 = "SELECT 
    s.vZona AS Zona,
    COUNT(DISTINCT s.idSiembra) AS TotalSiembras,
    COUNT(DISTINCT c.idCosecha) AS TotalCosechas,
    COALESCE(SUM(c.iKilos), 0) AS KilosProducidos
FROM siembras s
LEFT JOIN cosechas c ON s.idSiembra = c.idSiembra
GROUP BY s.vZona
ORDER BY KilosProducidos DESC";
$stmt1 = $db->prepare($query1);
$stmt1->execute();
$reporte1 = $stmt1->fetchAll();

// Reporte 2: Detalle de Siembras con sus Cosechas
$query2 = "SELECT 
    s.idSiembra,
    s.vNombre,
    s.dFecha AS FechaSiembra,
    s.vZona,
    c.idCosecha,
    c.dFecha AS FechaCosecha,
    c.iKilos
FROM siembras s
LEFT JOIN cosechas c ON s.idSiembra = c.idSiembra
ORDER BY s.dFecha DESC, c.dFecha DESC";
$stmt2 = $db->prepare($query2);
$stmt2->execute();
$reporte2 = $stmt2->fetchAll();

// Reporte 3: Siembras más Productivas
$query3 = "SELECT 
    s.idSiembra,
    s.vNombre,
    s.vZona,
    COUNT(c.idCosecha) AS NumeroCosechas,
    COALESCE(SUM(c.iKilos), 0) AS TotalKilos,
    COALESCE(AVG(c.iKilos), 0) AS PromedioKilos
FROM siembras s
LEFT JOIN cosechas c ON s.idSiembra = c.idSiembra
GROUP BY s.idSiembra
ORDER BY TotalKilos DESC";
$stmt3 = $db->prepare($query3);
$stmt3->execute();
$reporte3 = $stmt3->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes de Siembras</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<!-- Reporte 1: Producción por Zona -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="bi bi-geo-alt"></i> Producción por Zona</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Total Siembras</th>
                        <th>Total Cosechas</th>
                        <th>Kilos Producidos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte1 as $row): ?>
                    <tr>
                        <td><span class="badge bg-success"><?php echo $row['Zona']; ?></span></td>
                        <td><?php echo $row['TotalSiembras']; ?></td>
                        <td><?php echo $row['TotalCosechas']; ?></td>
                        <td><strong><?php echo number_format($row['KilosProducidos'], 2); ?> kg</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 2: Detalle de Siembras con Cosechas -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="bi bi-list-ul"></i> Detalle de Siembras y sus Cosechas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaDetalle" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID Siembra</th>
                        <th>Nombre Siembra</th>
                        <th>Fecha Siembra</th>
                        <th>Zona</th>
                        <th>ID Cosecha</th>
                        <th>Fecha Cosecha</th>
                        <th>Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte2 as $row): ?>
                    <tr>
                        <td><?php echo $row['idSiembra']; ?></td>
                        <td><?php echo htmlspecialchars($row['vNombre']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['FechaSiembra'])); ?></td>
                        <td><?php echo $row['vZona']; ?></td>
                        <td><?php echo $row['idCosecha'] ?: '-'; ?></td>
                        <td><?php echo $row['FechaCosecha'] ? date('d/m/Y', strtotime($row['FechaCosecha'])) : '-'; ?></td>
                        <td><?php echo $row['iKilos'] ? number_format($row['iKilos'], 2) . ' kg' : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 3: Siembras Más Productivas -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5><i class="bi bi-star"></i> Siembras Más Productivas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Zona</th>
                        <th>Número de Cosechas</th>
                        <th>Total Kilos</th>
                        <th>Promedio por Cosecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte3 as $row): ?>
                    <tr>
                        <td><?php echo $row['idSiembra']; ?></td>
                        <td><?php echo htmlspecialchars($row['vNombre']); ?></td>
                        <td><?php echo $row['vZona']; ?></td>
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
