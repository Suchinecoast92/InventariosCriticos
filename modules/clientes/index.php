<?php 
require_once '../../config/database_servidor.php';
include '../../includes/header.php';

$database = new DatabaseServidor();
$db = $database->getConnection();

// Obtener todos los clientes
$query = "SELECT * FROM clientes ORDER BY idCliente DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$clientes = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-people"></i> Gestión de Clientes</h2>
        <small class="text-muted"><i class="bi bi-cloud-fill"></i> Servidor Remoto</small>
    </div>
    <div class="col-md-6 text-end">
        <a href="create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Nuevo Cliente</a>
        <a href="reportes.php" class="btn btn-info"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaClientes" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RFC</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['idCliente']; ?></td>
                        <td><i class="bi bi-cloud-fill text-info"></i> <?php echo htmlspecialchars($cliente['vNombre']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['vRFC'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($cliente['vTelefono'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($cliente['vDireccion'] ?? '-'); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $cliente['idCliente']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="delete.php?id=<?php echo $cliente['idCliente']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar este cliente?')">
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
    $('#tablaClientes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
