<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener cosechas con información de siembra (JOIN)
$query = "SELECT c.*, s.vNombre AS nombreSiembra, s.vZona 
          FROM cosechas c 
          INNER JOIN siembras s ON c.idSiembra = s.idSiembra 
          ORDER BY c.dFecha DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$cosechas = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-basket"></i> Gestión de Cosechas</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Cosecha</a>
        <a href="reportes.php" class="btn btn-info"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaCosechas" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Siembra</th>
                        <th>Zona</th>
                        <th>Fecha Cosecha</th>
                        <th>Kilos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cosechas as $cosecha): ?>
                    <tr>
                        <td><?php echo $cosecha['idCosecha']; ?></td>
                        <td><?php echo htmlspecialchars($cosecha['nombreSiembra']); ?></td>
                        <td><span class="badge bg-primary"><?php echo $cosecha['vZona']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($cosecha['dFecha'])); ?></td>
                        <td><strong><?php echo number_format($cosecha['iKilos'], 2); ?> kg</strong></td>
                        <td>
                            <a href="edit.php?id=<?php echo $cosecha['idCosecha']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $cosecha['idCosecha']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar esta cosecha?')">
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
    $('#tablaCosechas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
