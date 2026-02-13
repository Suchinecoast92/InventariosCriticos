-- =====================================================
-- BASE DE DATOS SERVIDOR (UNIVERSIDAD)
-- Sistema de Trazabilidad del Limón
-- Fragmentación Vertical: Información de Clientes
-- =====================================================

-- Crear la base de datos
DROP DATABASE IF EXISTS sistema_limon_s;
CREATE DATABASE sistema_limon_s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_limon_s;

-- =====================================================
-- TABLA: Clientes (Fragmentación Vertical)
-- =====================================================
CREATE TABLE clientes (
    idCliente INT AUTO_INCREMENT PRIMARY KEY,
    vNombre VARCHAR(100) NOT NULL,
    vTelefono VARCHAR(15),
    vDireccion VARCHAR(200),
    vRFC VARCHAR(13)
) ENGINE=InnoDB;

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================
INSERT INTO clientes (vNombre, vTelefono, vDireccion, vRFC) VALUES
('Comercializadora Citricos del Norte SA de CV', '8121234567', 'Av. Constitución 100, Monterrey, NL', 'CCN950101ABC'),
('Distribuidora Frutas Frescas', '8187654321', 'Blvd. Díaz Ordaz 200, San Pedro, NL', 'DFF980615XYZ'),
('Supermercados La Economía', '8145678901', 'Av. Universidad 500, Monterrey, NL', 'SLE920310DEF'),
('Jugos y Néctares Industriales SA', '8156789012', 'Carretera Nacional Km 10, Apodaca, NL', 'JNI000205GHI'),
('Mercado Central de Abastos', '8134567890', 'Av. Sendero Norte 300, Monterrey, NL', 'MCA870520JKL');
