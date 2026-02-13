<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Reporte 1: Lotes por zona con estadísticas
$query1 = "SELECT 
    s.vZona,
    COUNT(DISTINCT l.idLote) AS TotalLotes,
    SUM(l.iKilos) AS TotalKilos,
    SUM(COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0)) AS KilosVendidos,
    SUM(COALESCE((SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote), 0)) AS KilosMermas,
    SUM(l.iKilos - COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) - 
        COALESCE((SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote), 0)) AS KilosDisponibles
FROM lotes l
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
GROUP BY s.vZona
ORDER BY TotalLotes DESC";
$stmt1 = $db->prepare($query1);
$stmt1->execute();
$reporte1 = $stmt1->fetchAll();

// Reporte 2: Lotes con mayor rotación (más vendidos)
$query2 = "SELECT 
    l.idLote,
    l.dFechaEmpaque,
    l.iKilos AS KilosEmpacados,
    s.vNombre AS Siembra,
    s.vZona,
    COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) AS KilosVendidos,
    COALESCE((SELECT COUNT(*) FROM ventas v WHERE v.idLote = l.idLote), 0) AS NumeroVentas,
    (COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) / l.iKilos * 100) AS PorcentajeVendido
FROM lotes l
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
HAVING KilosVendidos > 0
ORDER BY PorcentajeVendido DESC, NumeroVentas DESC
LIMIT 10";
$stmt2 = $db->prepare($query2);
$stmt2->execute();
$reporte2 = $stmt2->fetchAll();

// Reporte 3: Lotes con mayor disponibilidad
$query3 = "SELECT 
    l.idLote,
    l.dFechaEmpaque,
    l.iKilos AS KilosEmpacados,
    s.vNombre AS Siembra,
    s.vZona,
    COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) AS KilosVendidos,
    COALESCE((SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote), 0) AS KilosMermas,
    (l.iKilos - COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) - 
     COALESCE((SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote), 0)) AS KilosDisponibles
FROM lotes l
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
HAVING KilosDisponibles > 0
ORDER BY KilosDisponibles DESC";
$stmt3 = $db->prepare($query3);
$stmt3->execute();
$reporte3 = $stmt3->fetchAll();

// Reporte 4: Estado de lotes por mes
$query4 = "SELECT 
    DATE_FORMAT(l.dFechaEmpaque, '%Y-%m') AS Mes,
    COUNT(l.idLote) AS TotalLotes,
    SUM(l.iKilos) AS KilosEmpacados,
    SUM(COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0)) AS KilosVendidos,
    AVG(l.iKilos) AS PromedioKilosPorLote
FROM lotes l
GROUP BY DATE_FORMAT(l.dFechaEmpaque, '%Y-%m')
ORDER BY Mes DESC
LIMIT 12";
$stmt4 = $db->prepare($query4);
$stmt4->execute();
$reporte4 = $stmt4->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes de Lotes</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<!-- Reporte 1: Lotes por Zona -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="bi bi-geo-alt"></i> Lotes por Zona</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Total Lotes</th>
                        <th>Total Kilos</th>
                        <th>Vendidos</th>
                        <th>Mermas</th>
                        <th>Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte1 as $row): ?>
                    <tr>
                        <td><span class="badge bg-success"><?php echo $row['vZona']; ?></span></td>
                        <td><strong><?php echo $row['TotalLotes']; ?></strong></td>
                        <td><?php echo number_format($row['TotalKilos'], 2); ?> kg</td>
                        <td><?php echo number_format($row['KilosVendidos'], 2); ?> kg</td>
                        <td><?php echo number_format($row['KilosMermas'], 2); ?> kg</td>
                        <td><strong><?php echo number_format($row['KilosDisponibles'], 2); ?> kg</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 2: Lotes con Mayor Rotación -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="bi bi-arrow-repeat"></i> Top 10 Lotes con Mayor Rotación</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Lote</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Fecha Empaque</th>
                        <th>Empacado</th>
                        <th>Vendido</th>
                        <th>Núm. Ventas</th>
                        <th>% Vendido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte2 as $row): ?>
                    <tr>
                        <td><strong>#<?php echo $row['idLote']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['Siembra']); ?></td>
                        <td><span class="badge bg-success"><?php echo $row['vZona']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($row['dFechaEmpaque'])); ?></td>
                        <td><?php echo number_format($row['KilosEmpacados'], 2); ?> kg</td>
                        <td><?php echo number_format($row['KilosVendidos'], 2); ?> kg</td>
                        <td><span class="badge bg-info"><?php echo $row['NumeroVentas']; ?></span></td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?php echo min($row['PorcentajeVendido'], 100); ?>%">
                                    <?php echo number_format($row['PorcentajeVendido'], 1); ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 3: Lotes con Mayor Disponibilidad -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="bi bi-box-seam"></i> Lotes con Mayor Disponibilidad</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Lote</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Fecha Empaque</th>
                        <th>Empacado</th>
                        <th>Vendido</th>
                        <th>Mermas</th>
                        <th>Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte3 as $row): ?>
                    <tr>
                        <td><strong>#<?php echo $row['idLote']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['Siembra']); ?></td>
                        <td><span class="badge bg-success"><?php echo $row['vZona']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($row['dFechaEmpaque'])); ?></td>
                        <td><?php echo number_format($row['KilosEmpacados'], 2); ?> kg</td>
                        <td><?php echo number_format($row['KilosVendidos'], 2); ?> kg</td>
                        <td><?php echo number_format($row['KilosMermas'], 2); ?> kg</td>
                        <td><strong class="text-success"><?php echo number_format($row['KilosDisponibles'], 2); ?> kg</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reporte 4: Lotes por Mes -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5><i class="bi bi-calendar-month"></i> Estado de Lotes por Mes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Total Lotes</th>
                        <th>Kilos Empacados</th>
                        <th>Kilos Vendidos</th>
                        <th>Promedio por Lote</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte4 as $row): ?>
                    <tr>
                        <td><strong><?php echo date('M Y', strtotime($row['Mes'] . '-01')); ?></strong></td>
                        <td><?php echo $row['TotalLotes']; ?></td>
                        <td><?php echo number_format($row['KilosEmpacados'], 2); ?> kg</td>
                        <td><?php echo number_format($row['KilosVendidos'], 2); ?> kg</td>
                        <td><?php echo number_format($row['PromedioKilosPorLote'], 2); ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
