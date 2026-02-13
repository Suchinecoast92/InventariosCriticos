<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener mermas con JOIN a lotes, cosechas y siembras
$query = "SELECT 
    m.idMerma,
    m.iCantidad,
    m.vTipoMerma,
    m.dFecha,
    l.idLote,
    c.idCosecha,
    s.vNombre AS nombreSiembra,
    s.vZona
FROM mermas m
INNER JOIN lotes l ON m.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
ORDER BY m.dFecha DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$mermas = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-trash"></i> Gestión de Mermas</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="create.php" class="btn btn-danger"><i class="bi bi-plus-circle"></i> Nueva Merma</a>
        <a href="reportes.php" class="btn btn-info"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaMermas" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Lote</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Tipo Merma</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mermas as $merma): ?>
                    <tr>
                        <td><?php echo $merma['idMerma']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($merma['dFecha'])); ?></td>
                        <td><?php echo $merma['idLote']; ?></td>
                        <td><?php echo htmlspecialchars($merma['nombreSiembra']); ?></td>
                        <td><span class="badge bg-danger"><?php echo $merma['vZona']; ?></span></td>
                        <td><?php echo htmlspecialchars($merma['vTipoMerma']); ?></td>
                        <td><strong class="text-danger"><?php echo number_format($merma['iCantidad'], 2); ?> kg</strong></td>
                        <td>
                            <a href="edit.php?id=<?php echo $merma['idMerma']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $merma['idMerma']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar este registro de merma?')">
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
    $('#tablaMermas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
