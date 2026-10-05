CREATE TABLE insumos (
    id_insumo INT AUTO_INCREMENT,
    descripcion VARCHAR(200) NOT NULL,
    cantidad_stock INT NOT NULL DEFAULT 0,
    categoria ENUM('Alimentos', 'Ropa', 'Medicamentos', 'Sanitarios') NOT NULL,
    PRIMARY KEY (id_insumo),
    CHECK (cantidad_stock >= 0)
);
CREATE TABLE donantes (
    id_donante INT AUTO_INCREMENT,
    nombre_razon_social VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30),
    PRIMARY KEY (id_donante)
);
CREATE TABLE despachos (
    numero_remito INT AUTO_INCREMENT,
    fecha_hora DATETIME NOT NULL,
    destino VARCHAR(150) NOT NULL,
    PRIMARY KEY (numero_remito)
);
CREATE TABLE detalle_despachos (
    id_detalle INT AUTO_INCREMENT,
    numero_remito INT NOT NULL,
    id_insumo INT NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (id_detalle),
    FOREIGN KEY (numero_remito) REFERENCES despachos(numero_remito),
    FOREIGN KEY (id_insumo) REFERENCES insumos(id_insumo),
    CHECK (cantidad >= 1)
);
CREATE TABLE ingresos (
    id_ingreso INT AUTO_INCREMENT,
    id_donante INT NOT NULL,
    id_insumo INT NOT NULL,
    cantidad INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    PRIMARY KEY (id_ingreso),
    FOREIGN KEY (id_donante) REFERENCES donantes(id_donante),
    FOREIGN KEY (id_insumo) REFERENCES insumos(id_insumo),
    CHECK (cantidad >= 1)
);