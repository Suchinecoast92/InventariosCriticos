-- =====================================================
-- BASE DE DATOS LOCAL (LAPTOP ESTUDIANTE)
-- Sistema de Trazabilidad del Limón
-- =====================================================

-- Crear la base de datos
DROP DATABASE IF EXISTS sistema_limon_l;
CREATE DATABASE sistema_limon_l CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_limon_l;

-- =====================================================
-- TABLA: Siembras
-- =====================================================
CREATE TABLE siembras (
    idSiembra INT AUTO_INCREMENT PRIMARY KEY,
    vNombre VARCHAR(100) NOT NULL,
    dFecha DATE NOT NULL,
    vZona VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Cosechas
-- =====================================================
CREATE TABLE cosechas (
    idCosecha INT AUTO_INCREMENT PRIMARY KEY,
    idSiembra INT NOT NULL,
    dFecha DATE NOT NULL,
    iKilos DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (idSiembra) REFERENCES siembras(idSiembra)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Lotes
-- =====================================================
CREATE TABLE lotes (
    idLote INT AUTO_INCREMENT PRIMARY KEY,
    idCosecha INT NOT NULL,
    iKilos DECIMAL(10,2) NOT NULL,
    dFechaEmpaque DATE NOT NULL,
    FOREIGN KEY (idCosecha) REFERENCES cosechas(idCosecha)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Ventas
-- =====================================================
CREATE TABLE ventas (
    idVenta INT AUTO_INCREMENT PRIMARY KEY,
    idLote INT NOT NULL,
    idCliente INT NOT NULL,
    dFechaVenta DATE NOT NULL,
    iKilos DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (idLote) REFERENCES lotes(idLote)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Mermas
-- =====================================================
CREATE TABLE mermas (
    idMerma INT AUTO_INCREMENT PRIMARY KEY,
    idLote INT NOT NULL,
    iCantidad DECIMAL(10,2) NOT NULL,
    vTipoMerma VARCHAR(50) NOT NULL,
    dFecha DATE NOT NULL,
    FOREIGN KEY (idLote) REFERENCES lotes(idLote)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA FEDERADA: Clientes (apunta al servidor)
-- =====================================================
CREATE TABLE clientes_federados (
    idCliente INT NOT NULL,
    vNombre VARCHAR(100),
    vTelefono VARCHAR(15),
    vDireccion VARCHAR(200),
    vRFC VARCHAR(13)
) ENGINE=FEDERATED
CONNECTION='mysql://admin:informatica@10.20.41.160/sistema_limon_s/clientes';

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================
INSERT INTO siembras (vNombre, dFecha, vZona) VALUES
('Siembra Zona Norte 2024', '2024-01-15', 'NORTE'),
('Siembra Zona Sur 2024', '2024-02-01', 'SUR'),
('Siembra Zona Este 2024', '2024-03-10', 'ESTE');

INSERT INTO cosechas (idSiembra, dFecha, iKilos) VALUES
(1, '2024-07-20', 500.50),
(1, '2024-08-15', 450.75),
(2, '2024-08-01', 320.00),
(3, '2024-09-05', 280.25);

INSERT INTO lotes (idCosecha, iKilos, dFechaEmpaque) VALUES
(1, 250.00, '2024-07-21'),
(1, 250.50, '2024-07-22'),
(2, 300.00, '2024-08-16'),
(2, 150.75, '2024-08-16'),
(3, 320.00, '2024-08-02'),
(4, 280.25, '2024-09-06');

INSERT INTO ventas (idLote, idCliente, dFechaVenta, iKilos) VALUES
(1, 1, '2024-07-25', 250.00),
(2, 2, '2024-07-28', 250.50),
(3, 3, '2024-08-20', 300.00),
(5, 1, '2024-08-10', 320.00);

INSERT INTO mermas (idLote, iCantidad, vTipoMerma, dFecha) VALUES
(4, 50.75, 'Maduración excesiva', '2024-08-18'),
(6, 30.00, 'Daño físico', '2024-09-08'),
(4, 20.00, 'Plagas', '2024-08-20');

-- =====================================================
-- FIN DEL SCRIPT DE BASE DE DATOS LOCAL
-- =====================================================
