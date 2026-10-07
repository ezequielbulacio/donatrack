# Dona-Track ONG

Plataforma web para la gestión, auditoría y control de stock de suministros y donaciones de una ONG. Trabajo Final para Alumno Libre — Proyecto Informático, UPSO.

## Caso de negocio

Dona-Track permite registrar insumos donados, controlar el inventario en tiempo real y emitir remitos en PDF cuando los materiales son despachados a centros de ayuda comunitarios.

## Tecnologías

- PHP 8 + PDO (MySQL)
- MySQL
- Bootstrap 5
- DomPDF (vía Composer) — generación de remitos en PDF
- Git

## Estructura del proyecto

```
donatrack/
├── config/         → configuración de la app (ambiente, conexión a BD)
├── database/       → script SQL con el modelo de datos
├── includes/       → código PHP reutilizable (conexión, errores, header, footer)
├── logs/           → logs de errores (solo en ambiente PRODUCTION)
├── public/         → páginas que ve el usuario
└── vendor/         → librerías de Composer (no versionado)
```

## Instalación

1. Clonar el repositorio dentro de `htdocs` de XAMPP.
2. Instalar las dependencias con Composer:
```
   composer install
```
3. Crear la base de datos `donatrack_db` en MySQL y ejecutar `database/schema.sql`.
4. Crear `config/config.json` (no versionado) con la siguiente estructura:
```json
   {
     "app_name": "Dona-Track ONG",
     "environment": "DEVELOPMENT",
     "database": {
       "host": "localhost",
       "db_name": "donatrack_db",
       "user": "root",
       "password": "",
       "charset": "utf8mb4"
     },
     "log_file": "logs/sistema_error.log"
   }
```
5. Acceder a `http://localhost/donatrack/public/inventario.php`

## Modelo de datos

El enunciado define 3 entidades (Insumos, Donantes, Despachos). Se agregaron 2 tablas intermedias para modelar las relaciones muchos-a-muchos:

- **insumos**: stock disponible por material, con restricción `CHECK` de que no sea negativo.
- **donantes**: identificados por email único.
- **despachos**: cabecera de cada entrega (fecha, destino).
- **detalle_despachos**: relaciona cada despacho con los insumos y cantidades despachadas (un despacho puede incluir varios insumos).
- **ingresos**: relaciona cada donante con los insumos y cantidades donadas (necesaria porque el ingreso de un insumo se asocia a un donante específico).

## Ambientes

La app reconoce 3 ambientes mediante la constante `APP_ENV`, definida en `config/config.json`:

- **DEVELOPMENT / TESTING**: los errores técnicos se muestran en pantalla (caja de debug).
- **PRODUCTION**: se muestra un mensaje genérico al usuario, y el error real se guarda con fecha y hora en `logs/sistema_error.log`.

## Funcionalidades

- Registrar ingreso/donación (actualiza stock)
- Registrar despacho de materiales (valida stock suficiente antes de confirmar)
- Listado de inventario en tiempo real
- Generación de remito en PDF al confirmar un despacho
- Historial de logs y auditoría