<?php include 'includes/header.php'; ?>

<div class="row mb-5">
    <div class="col-12">
        <h1 class="display-4">Sistema de Trazabilidad del Limón</h1>
        <p class="lead">Gestión completa del ciclo de vida del limón</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-flower2"></i> Siembras</h5>
                <p class="card-text text-muted">Registro y control de siembras</p>
                <a href="/SistemaLIMON/modules/siembras/" class="btn btn-success btn-sm">Acceder</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-basket"></i> Cosechas</h5>
                <p class="card-text text-muted">Gestión de cosechas</p>
                <a href="/SistemaLIMON/modules/cosechas/" class="btn btn-primary btn-sm">Acceder</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-box-seam"></i> Lotes</h5>
                <p class="card-text text-muted">Empaque y gestión de lotes</p>
                <a href="/SistemaLIMON/modules/lotes/" class="btn btn-secondary btn-sm">Acceder</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cart"></i> Ventas</h5>
                <p class="card-text text-muted">Control de ventas</p>
                <a href="/SistemaLIMON/modules/ventas/" class="btn btn-warning btn-sm">Acceder</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Mermas</h5>
                <p class="card-text text-muted">Registro de mermas</p>
                <a href="/SistemaLIMON/modules/mermas/" class="btn btn-danger btn-sm">Acceder</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cloud-fill text-info"></i> Clientes</h5>
                <p class="card-text text-muted">Gestión de clientes (Servidor)</p>
                <a href="/SistemaLIMON/modules/clientes/" class="btn btn-info btn-sm">Acceder</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-diagram-3"></i> Trazabilidad</h5>
                <p class="card-text text-muted">Seguimiento por lote</p>
                <a href="/SistemaLIMON/modules/trazabilidad/" class="btn btn-dark btn-sm">Consultar</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
