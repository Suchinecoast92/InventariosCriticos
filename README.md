# Sistema Web de Trazabilidad del Limón

Sistema completo de gestión y trazabilidad del limón, abarcando desde la siembra hasta la venta final, con base de datos federada.

## Características Principales

- **Gestión Completa de Siembras** - CRUD y reportes detallados
- **Gestión de Cosechas** - Control de producción por zona
- **Gestión de Lotes** - Empaque y control de disponibilidad
- **Gestión de Ventas** - Integración con datos federados de clientes
- **Gestión de Mermas** - Registro y análisis de pérdidas
- **Gestión de Clientes** - CRUD completo en servidor remoto
- **Trazabilidad por Lote** - Seguimiento completo del ciclo de vida
- **Reportes con JOINs** - Análisis detallados en cada módulo
- **Base de Datos Federada** - Integración servidor-local para clientes

## Estructura del Proyecto

```
SistemaLIMON/
├── config/
│   ├── database.php          # Configuración BD local
│   └── database_servidor.php # Configuración BD servidor
├── includes/
│   ├── header.php            # Header con navegación
│   └── footer.php            # Footer del sistema
├── modules/
│   ├── siembras/            # Módulo de siembras
│   │   ├── index.php        # Listado
│   │   ├── create.php       # Crear
│   │   ├── edit.php         # Editar
│   │   ├── delete.php       # Eliminar
│   │   └── reportes.php     # Reportes con JOINs
│   ├── cosechas/            # Módulo de cosechas
│   ├── lotes/               # Módulo de lotes (empaque)
│   ├── ventas/              # Módulo de ventas (con federación)
│   ├── mermas/              # Módulo de mermas
│   ├── clientes/            # Módulo de clientes (servidor)
│   └── trazabilidad/        # Trazabilidad por lote
├── assets/
│   └── css/
│       └── style.css        # Estilos personalizados
├── sql/                     # Scripts SQL
└── index.php               # Página principal
```

## Instalación

### 1. Requisitos Previos
- XAMPP con PHP 7.4+ y MySQL
- Navegador web moderno
- Bases de datos creadas: `sistema_limon_l` (local) y `sistema_limon_s` (servidor)

### 2. Configurar Base de Datos

Ejecutar los scripts SQL en el siguiente orden:

```sql
-- En el servidor (universidad)
sql/02_base_datos_servidor.sql

-- En la máquina local
sql/01_base_datos_local.sql
```

### 3. Habilitar FEDERATED Engine

Editar `C:\xampp\mysql\bin\my.ini` y agregar:
```ini
[mysqld]
federated
```

Reiniciar MySQL desde el panel de XAMPP.

### 4. Configurar Conexión

Editar `config/database.php` para la BD local:
```php
private $host = "localhost";
private $db_name = "sistema_limon_l";
private $username = "root";
private $password = "";
```

Editar `config/database_servidor.php` para la BD del servidor:
```php
private $host = "10.20.41.160";
private $db_name = "limon_servidor";
private $username = "admin";
private $password = "informatica";
```

### 5. Acceder al Sistema

Abrir en el navegador:
```
http://localhost/SistemaLIMON/
```

## 📊 Módulos del Sistema

### 1. Siembras
- CRUD completo de siembras
- Reportes:
  - Producción por zona
  - Detalle de siembras con cosechas
  - Siembras más productivas

### 2. Cosechas
- CRUD completo de cosechas
- Asociadas a siembras mediante JOIN
- Reportes:
  - Cosechas por zona
  - Cosechas por mes
  - Detalle con siembras y lotes
  - Top cosechas más grandes

### 3. Lotes
- **CRUD completo de lotes (empaque)**
- Validación de kilos disponibles por cosecha
- Cálculo automático de disponibilidad (empacado - vendido - mermas)
- Reportes:
  - Lotes por zona con estadísticas
  - Top lotes con mayor rotación
  - Lotes con mayor disponibilidad
  - Estado de lotes por mes

### 4. Ventas
- CRUD completo de ventas
- **Integración con tabla federada de clientes**
- Reportes:
  - Ventas por cliente (datos federados)
  - Ventas por zona
  - Trazabilidad completa
  - Ventas por mes

### 5. Mermas
- CRUD completo de mermas
- Reportes:
  - Mermas por tipo
  - Mermas por zona
  - Mermas mensuales
  - Trazabilidad de mermas

### 6. Clientes
- **CRUD completo en servidor remoto**
- Operaciones directas a base de datos servidor
- Validación de integridad referencial
- Reportes:
  - Top clientes por compras
  - Clientes sin compras
  - Estadísticas generales
  - Compras por zona de origen

### 7. Trazabilidad
- Seguimiento completo por lote
- Línea de tiempo desde siembra hasta venta
- Visualización de ventas y mermas
- Estadísticas de disponibilidad

## 🔗 Integración Federada

El sistema utiliza una tabla federada para acceder a los datos de clientes almacenados en el servidor:

```sql
-- La tabla clientes_federados en local apunta al servidor
CREATE TABLE clientes_federados (...)
ENGINE=FEDERATED
CONNECTION='mysql://admin:informatica@10.20.41.160/sistema_limon_s/clientes';
```

**Nota:** Asegúrese de que:
- El servidor esté accesible
- Las credenciales sean correctas
- El motor FEDERATED esté habilitado

## 🎨 Tecnologías Utilizadas

- **Backend:** PHP 7.4+ con PDO
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Iconos:** Bootstrap Icons
- **Tablas:** DataTables con paginación y búsqueda
- **Base de Datos:** MySQL con tablas federadas

## 📋 Reglas de Negocio

1. Toda cosecha debe pertenecer a una siembra
2. Todo lote debe pertenecer a una cosecha
3. Los kilos empacados en lotes no pueden exceder los kilos disponibles de la cosecha
4. Toda venta debe estar asociada a un lote
5. No se pueden registrar ventas sin cliente (tabla federada)
6. Las mermas deben estar asociadas a un lote existente
7. No se puede eliminar un lote que tenga ventas o mermas asociadas
8. La disponibilidad de un lote = kilos empacados - kilos vendidos - kilos de mermas

## 🔍 Consultas con JOINs

Cada módulo incluye reportes que utilizan JOINs para relacionar datos:

- **INNER JOIN:** Relaciones obligatorias (siembras-cosechas)
- **LEFT JOIN:** Relaciones opcionales (siembras sin cosechas)
- **Múltiples JOINs:** Trazabilidad completa (siembra → cosecha → lote → venta/merma)

## 👨‍💻 Uso del Sistema

1. **Iniciar con Siembras:** Registrar las siembras de limón
2. **Registrar Cosechas:** Asociar cosechas a las siembras
3. **Crear Lotes:** Empacar las cosechas en lotes
4. **Registrar Ventas:** Vender lotes a clientes
5. **Reportar Mermas:** Registrar pérdidas por lote
6. **Consultar Trazabilidad:** Ver el recorrido completo de cada lote

## 📝 Notas Importantes

- **Clientes**: Gestión completa (CRUD) directamente en el servidor remoto
  - Lecturas: Se pueden usar desde tabla federada local
  - Escrituras: Se ejecutan directamente en el servidor
  - Validación: Verifica integridad referencial con ventas antes de eliminar
- El sistema funciona con o sin conexión al servidor (algunas funciones limitadas)
- Los reportes utilizan consultas optimizadas con índices
- DataTables permite búsqueda y ordenamiento en todas las tablas
- **Dos conexiones de BD**: Una local y otra al servidor remoto

## 🆘 Solución de Problemas

### Error de conexión federada
```
Verificar:
1. Servidor MySQL remoto accesible
2. Usuario 'admin' con permisos
3. FEDERATED engine habilitado
4. IP del servidor correcta
```

### No aparecen datos
```
Verificar:
1. Scripts SQL ejecutados correctamente
2. Datos de ejemplo insertados
3. Configuración de database.php correcta
```

### Error en módulo de clientes
```
Verificar:
1. Archivo database_servidor.php configurado correctamente
2. IP del servidor: 10.20.41.160
3. Base de datos: limon_servidor
4. Usuario 'admin' con permisos de escritura
5. Tabla 'clientes' existe en el servidor
```

## 📧 Soporte

Para asistencia adicional, revisar los comentarios en el código fuente o contactar al administrador del sistema.

---
**© 2024 Sistema de Trazabilidad del Limón**

