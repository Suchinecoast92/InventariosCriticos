<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener lotes con información de cosecha y siembra (JOINs)
$query = "SELECT 
    l.*,
    c.dFecha as fechaCosecha,
    s.vNombre as nombreSiembra,
    s.vZona,
    (SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote) as kilosVendidos,
    (SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote) as kilosMermados,
    (l.iKilos - COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) - 
     COALESCE((SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote), 0)) as kilosDisponibles
FROM lotes l
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
ORDER BY l.dFechaEmpaque DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$lotes = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-box-seam"></i> Gestión de Lotes</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Nuevo Lote</a>
        <a href="reportes.php" class="btn btn-info"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
    </div>
</div>

<!-- Estadísticas rápidas -->
<?php
$queryStats = "SELECT 
    COUNT(*) as totalLotes,
    SUM(iKilos) as totalKilos,
    SUM(iKilos - COALESCE((SELECT SUM(v.iKilos) FROM ventas v WHERE v.idLote = l.idLote), 0) - 
        COALESCE((SELECT SUM(m.iCantidad) FROM mermas m WHERE m.idLote = l.idLote), 0)) as kilosDisponibles
FROM lotes l";
$stmtStats = $db->prepare($queryStats);
$stmtStats->execute();
$stats = $stmtStats->fetch();
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $stats['totalLotes']; ?></h3>
                <p class="mb-0">Total Lotes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-success"><?php echo number_format($stats['totalKilos'], 2); ?> kg</h3>
                <p class="mb-0">Total Empacado</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-info"><?php echo number_format($stats['kilosDisponibles'], 2); ?> kg</h3>
                <p class="mb-0">Disponible</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaLotes" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Lote</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Fecha Empaque</th>
                        <th>Kilos Total</th>
                        <th>Vendidos</th>
                        <th>Mermas</th>
                        <th>Disponible</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lotes as $lote): ?>
                    <tr>
                        <td><strong>#<?php echo $lote['idLote']; ?></strong></td>
                        <td><?php echo htmlspecialchars($lote['nombreSiembra']); ?></td>
                        <td><span class="badge bg-success"><?php echo $lote['vZona']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($lote['dFechaEmpaque'])); ?></td>
                        <td><strong><?php echo number_format($lote['iKilos'], 2); ?> kg</strong></td>
                        <td><?php echo number_format($lote['kilosVendidos'] ?? 0, 2); ?> kg</td>
                        <td><?php echo number_format($lote['kilosMermados'] ?? 0, 2); ?> kg</td>
                        <td>
                            <?php 
                            $disponible = $lote['kilosDisponibles'];
                            $badgeClass = $disponible > 0 ? 'bg-success' : 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo number_format($disponible, 2); ?> kg
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $lote['idLote']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="delete.php?id=<?php echo $lote['idLote']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar este lote?')">
                                <i class="bi bi-trash"></i> Eliminar
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
    $('#tablaLotes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']]
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
