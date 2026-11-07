USE donatrack_db;

INSERT INTO donantes (nombre_razon_social, email, telefono) VALUES
('Supermercado El Gauchito', 'contacto@elgauchito.com', '2954123456'),
('Juan Carlos Gómez', 'juancarlos@gmail.com', '2954654321');

INSERT INTO insumos (descripcion, cantidad_stock, categoria) VALUES
('Arroz 1kg', 0, 'Alimentos'),
('Frazadas', 0, 'Ropa'),
('Ibuprofeno 400mg', 0, 'Medicamentos'),
('Alcohol en gel 500ml', 0, 'Sanitarios');