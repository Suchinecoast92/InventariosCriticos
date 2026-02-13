<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Trazabilidad del Limón</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/SistemaLIMON/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/SistemaLIMON/">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="me-2" style="display: inline-block; vertical-align: middle;">
                    <circle cx="12" cy="12" r="10" fill="#FFD500"/>
                    <ellipse cx="12" cy="12" rx="6" ry="8" fill="#FFF176" opacity="0.5"/>
                    <path d="M12 2C11 2 10.5 3 10.5 4C10.5 5 11 6 12 6" stroke="#44a82c" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span style="vertical-align: middle;">Sistema Limón</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/SistemaLIMON/">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Módulos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/siembras/">Siembras</a></li>
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/cosechas/">Cosechas</a></li>
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/lotes/">Lotes</a></li>
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/ventas/">Ventas</a></li>
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/mermas/">Mermas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/clientes/"><i class="bi bi-cloud-fill text-info"></i> Clientes</a></li>
                            <li><a class="dropdown-item" href="/SistemaLIMON/modules/trazabilidad/">Trazabilidad</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid main-content mt-4">
