<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener todas las siembras
$query = "SELECT * FROM siembras ORDER BY dFecha DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$siembras = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-seed"></i> Gestión de Siembras</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Nueva Siembra</a>
        <a href="reportes.php" class="btn btn-info"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaSiembras" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Zona</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($siembras as $siembra): ?>
                    <tr>
                        <td><?php echo $siembra['idSiembra']; ?></td>
                        <td><?php echo htmlspecialchars($siembra['vNombre']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($siembra['dFecha'])); ?></td>
                        <td><span class="badge bg-success"><?php echo $siembra['vZona']; ?></span></td>
                        <td>
                            <a href="edit.php?id=<?php echo $siembra['idSiembra']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="delete.php?id=<?php echo $siembra['idSiembra']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar esta siembra?')">
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
    $('#tablaSiembras').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
