-- =====================================================
-- CONSULTAS CON JOINS - Reportes Solicitados
-- Sistema de Trazabilidad del Limón
-- =====================================================

USE sistema_limon_l;

-- =====================================================
-- 1. REPORTE: PRODUCCIÓN POR ZONA
-- =====================================================
SELECT 
    s.vZona AS 'Zona',
    COUNT(DISTINCT s.idSiembra) AS 'Total Siembras',
    COUNT(DISTINCT c.idCosecha) AS 'Total Cosechas',
    COALESCE(SUM(c.iKilos), 0) AS 'Kilos Producidos'
FROM siembras s
LEFT JOIN cosechas c ON s.idSiembra = c.idSiembra
GROUP BY s.vZona
ORDER BY 'Kilos Producidos' DESC;

-- =====================================================
-- 2. REPORTE: VENTAS POR CLIENTE (FEDERADO)
-- =====================================================
SELECT 
    cf.idCliente,
    cf.vNombre AS 'Cliente',
    cf.vTelefono AS 'Teléfono',
    cf.vRFC AS 'RFC',
    COUNT(v.idVenta) AS 'Número de Compras',
    SUM(v.iKilos) AS 'Total Kilos Comprados'
FROM clientes_federados cf
INNER JOIN ventas v ON cf.idCliente = v.idCliente
GROUP BY cf.idCliente
ORDER BY 'Total Kilos Comprados' DESC;

-- =====================================================
-- 3. REPORTE: MERMAS MENSUALES
-- =====================================================
SELECT 
    m.dFecha AS 'Fecha',
    s.vZona AS 'Zona',
    m.vTipoMerma AS 'Tipo Merma',
    COUNT(m.idMerma) AS 'Incidentes',
    SUM(m.iCantidad) AS 'Total Merma (Kg)'
FROM mermas m
INNER JOIN lotes l ON m.idLote = l.idLote
INNER JOIN cosechas c ON l.idCosecha = c.idCosecha
INNER JOIN siembras s ON c.idSiembra = s.idSiembra
GROUP BY m.dFecha, s.vZona, m.vTipoMerma
ORDER BY m.dFecha DESC, 'Total Merma (Kg)' DESC;

-- =====================================================
-- FIN DE CONSULTAS
-- =====================================================
