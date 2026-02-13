<?php 
require_once '../../config/database.php';
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener cosechas disponibles con información de siembra
$queryCosechas = "SELECT 
    c.idCosecha,
    c.dFecha,
    c.iKilos,
    s.vNombre as nombreSiembra,
    s.vZona,
    COALESCE((SELECT SUM(l.iKilos) FROM lotes l WHERE l.idCosecha = c.idCosecha), 0) as kilosEmpacados,
    (c.iKilos - COALESCE((SELECT SUM(l.iKilos) FROM lotes l WHERE l.idCosecha = c.idCosecha), 0)) as kilosDisponibles
FROM cosechas c
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
HAVING kilosDisponibles > 0
ORDER BY c.dFecha DESC";

$stmtCosechas = $db->prepare($queryCosechas);
$stmtCosechas->execute();
$cosechas = $stmtCosechas->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idCosecha = $_POST['idCosecha'];
    $iKilos = $_POST['iKilos'];
    $dFechaEmpaque = $_POST['dFechaEmpaque'];
    
    // Verificar que no se exceda la cantidad disponible
    $queryCheck = "SELECT 
        c.iKilos,
        COALESCE((SELECT SUM(l.iKilos) FROM lotes l WHERE l.idCosecha = c.idCosecha), 0) as kilosEmpacados
    FROM cosechas c WHERE c.idCosecha = :idCosecha";
    $stmtCheck = $db->prepare($queryCheck);
    $stmtCheck->bindParam(':idCosecha', $idCosecha);
    $stmtCheck->execute();
    $cosechaInfo = $stmtCheck->fetch();
    
    $disponible = $cosechaInfo['iKilos'] - $cosechaInfo['kilosEmpacados'];
    
    if ($iKilos > $disponible) {
        echo "<script>alert('Error: Solo hay " . number_format($disponible, 2) . " kg disponibles en esta cosecha');</script>";
    } else {
        $query = "INSERT INTO lotes (idCosecha, iKilos, dFechaEmpaque) 
                  VALUES (:idCosecha, :iKilos, :dFechaEmpaque)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':idCosecha', $idCosecha);
        $stmt->bindParam(':iKilos', $iKilos);
        $stmt->bindParam(':dFechaEmpaque', $dFechaEmpaque);
        
        if ($stmt->execute()) {
            $idLote = $db->lastInsertId();
            echo "<script>alert('Lote #" . $idLote . " creado exitosamente'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Error al crear el lote');</script>";
        }
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-box-seam-fill"></i> Nuevo Lote</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<?php if (count($cosechas) == 0): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> <strong>No hay cosechas disponibles</strong> para crear lotes.
    Por favor, registre primero una cosecha o verifique que las cosechas existentes no estén completamente empacadas.
    <br><br>
    <a href="../cosechas/create.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Registrar Nueva Cosecha
    </a>
</div>
<?php else: ?>

<div class="card">
    <div class="card-body">
        <form method="POST" id="formLote">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="idCosecha" class="form-label">Cosecha Origen <span class="text-danger">*</span></label>
                    <select class="form-select" id="idCosecha" name="idCosecha" required onchange="updateDisponible()">
                        <option value="">-- Seleccione una cosecha --</option>
                        <?php foreach($cosechas as $cosecha): ?>
                        <option value="<?php echo $cosecha['idCosecha']; ?>" 
                                data-disponible="<?php echo $cosecha['kilosDisponibles']; ?>"
                                data-zona="<?php echo $cosecha['vZona']; ?>"
                                data-fecha="<?php echo date('d/m/Y', strtotime($cosecha['dFecha'])); ?>">
                            Cosecha #<?php echo $cosecha['idCosecha']; ?> - 
                            <?php echo htmlspecialchars($cosecha['nombreSiembra']); ?> 
                            (<?php echo $cosecha['vZona']; ?>) - 
                            Disponible: <?php echo number_format($cosecha['kilosDisponibles'], 2); ?> kg
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div id="infoCosecha" class="alert alert-info" style="display: none;">
                <h6><i class="bi bi-info-circle"></i> Información de la Cosecha Seleccionada</h6>
                <p class="mb-1"><strong>Zona:</strong> <span id="infoZona">-</span></p>
                <p class="mb-1"><strong>Fecha Cosecha:</strong> <span id="infoFecha">-</span></p>
                <p class="mb-0"><strong>Kilos Disponibles:</strong> <span id="infoDisponible">-</span> kg</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="iKilos" class="form-label">Kilos a Empacar <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control" 
                           id="iKilos" name="iKilos" required 
                           placeholder="Ej: 50.00">
                    <small class="text-muted">Cantidad de kilos para este lote</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="dFechaEmpaque" class="form-label">Fecha de Empaque <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="dFechaEmpaque" name="dFechaEmpaque" 
                           required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Crear Lote
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateDisponible() {
    const select = document.getElementById('idCosecha');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('infoCosecha');
    
    if (select.value) {
        const disponible = option.getAttribute('data-disponible');
        const zona = option.getAttribute('data-zona');
        const fecha = option.getAttribute('data-fecha');
        
        document.getElementById('infoZona').textContent = zona;
        document.getElementById('infoFecha').textContent = fecha;
        document.getElementById('infoDisponible').textContent = parseFloat(disponible).toFixed(2);
        
        // Actualizar el max del input de kilos
        document.getElementById('iKilos').max = disponible;
        
        infoDiv.style.display = 'block';
    } else {
        infoDiv.style.display = 'none';
    }
}

// Validar antes de enviar
document.getElementById('formLote').addEventListener('submit', function(e) {
    const select = document.getElementById('idCosecha');
    const option = select.options[select.selectedIndex];
    const kilosInput = document.getElementById('iKilos');
    
    if (select.value) {
        const disponible = parseFloat(option.getAttribute('data-disponible'));
        const kilos = parseFloat(kilosInput.value);
        
        if (kilos > disponible) {
            e.preventDefault();
            alert('Error: Solo hay ' + disponible.toFixed(2) + ' kg disponibles en esta cosecha');
            kilosInput.focus();
        }
    }
});
</script>

<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
