-- =====================================================
-- CONFIGURACIÓN DE TABLA FEDERADA
-- Sistema de Trazabilidad del Limón
-- =====================================================
-- Este archivo debe ejecutarse en la BASE DE DATOS LOCAL
-- después de haber creado la tabla 'clientes' en el servidor

USE sistema_limon_l;

-- Habilitar FEDERATED ENGINE:
-- 1. Editar my.ini (Windows) o my.cnf (Linux)
-- 2. Agregar: federated
-- 3. Reiniciar MySQL
-- 4. Verificar: SHOW ENGINES;

-- Verificar conexión federada:
-- SELECT * FROM clientes_federados LIMIT 5;

-- =====================================================
-- FIN DEL SCRIPT DE CONFIGURACIÓN FEDERADA
-- =====================================================
