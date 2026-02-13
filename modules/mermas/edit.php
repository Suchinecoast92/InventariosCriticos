<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];

// Obtener lotes
$query = "SELECT l.idLote, l.iKilos, l.dFechaEmpaque, c.idCosecha, s.vNombre, s.vZona
          FROM lotes l
          INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
          INNER JOIN siembras s ON c.idSiembra = s.idSiembra
          ORDER BY l.dFechaEmpaque DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$lotes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idLote = $_POST['idLote'];
    $iCantidad = $_POST['iCantidad'];
    $vTipoMerma = $_POST['vTipoMerma'];
    $dFecha = $_POST['dFecha'];
    
    $query = "UPDATE mermas SET idLote = :idLote, iCantidad = :iCantidad, 
              vTipoMerma = :vTipoMerma, dFecha = :dFecha WHERE idMerma = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->bindParam(':iCantidad', $iCantidad);
    $stmt->bindParam(':vTipoMerma', $vTipoMerma);
    $stmt->bindParam(':dFecha', $dFecha);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=updated");
        exit;
    }
}

$query = "SELECT * FROM mermas WHERE idMerma = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$merma = $stmt->fetch();
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning">
                <h4><i class="bi bi-pencil"></i> Editar Merma</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Lote</label>
                        <select class="form-select" name="idLote" required>
                            <?php foreach($lotes as $lote): ?>
                            <option value="<?php echo $lote['idLote']; ?>" 
                                <?php if($lote['idLote'] == $merma['idLote']) echo 'selected'; ?>>
                                Lote #<?php echo $lote['idLote']; ?> - <?php echo htmlspecialchars($lote['vNombre']); ?> 
                                (<?php echo $lote['vZona']; ?>) - <?php echo $lote['iKilos']; ?> kg
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Merma</label>
                        <select class="form-select" name="vTipoMerma" required>
                            <option value="Maduración excesiva" <?php if($merma['vTipoMerma']=='Maduración excesiva') echo 'selected'; ?>>Maduración excesiva</option>
                            <option value="Daño físico" <?php if($merma['vTipoMerma']=='Daño físico') echo 'selected'; ?>>Daño físico</option>
                            <option value="Plagas" <?php if($merma['vTipoMerma']=='Plagas') echo 'selected'; ?>>Plagas</option>
                            <option value="Enfermedades" <?php if($merma['vTipoMerma']=='Enfermedades') echo 'selected'; ?>>Enfermedades</option>
                            <option value="Mal almacenamiento" <?php if($merma['vTipoMerma']=='Mal almacenamiento') echo 'selected'; ?>>Mal almacenamiento</option>
                            <option value="Transporte" <?php if($merma['vTipoMerma']=='Transporte') echo 'selected'; ?>>Transporte</option>
                            <option value="Otros" <?php if($merma['vTipoMerma']=='Otros') echo 'selected'; ?>>Otros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad de Merma (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="iCantidad" 
                               value="<?php echo $merma['iCantidad']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="dFecha" 
                               value="<?php echo $merma['dFecha']; ?>" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
