# Dona-Track ONG

Plataforma web para la gestión, auditoría y control de stock de suministros y donaciones de una ONG. Trabajo Final para Alumno Libre — Proyecto Informático, UPSO.

## Caso de negocio

Dona-Track permite registrar insumos donados, controlar el inventario en tiempo real y emitir remitos en PDF cuando los materiales son despachados a centros de ayuda comunitarios.

## Tecnologías

- PHP 8 + PDO
- MySQL
- Bootstrap 5
- DomPDF (vía Composer) — generación de remitos en PDF
- Git / GitHub

## Estructura del proyecto

```
donatrack/
├── config/         → configuración (config.example.json es la plantilla)
├── database/       → schema.sql (tablas) y datos_prueba.sql (datos de ejemplo)
├── includes/       → código reutilizable (configuración, errores y logs, header, footer)
├── logs/           → sistema_error.log (se completa según el ambiente)
├── public/         → páginas que ve el usuario
├── composer.json   → dependencias (DomPDF)
└── vendor/         → librerías instaladas por Composer (no versionado)
```

## Instalación

Requisitos: XAMPP (Apache + MySQL + PHP 8) y Composer.

1. Clonar el repositorio dentro de `htdocs` de XAMPP:
   ```
   git clone https://github.com/ezequielbulacio/donatrack.git
   ```
2. Instalar las dependencias:
   ```
   composer install
   ```
3. Iniciar **Apache** y **MySQL** desde el panel de XAMPP.
4. Crear el archivo de configuración copiando la plantilla:
   ```
   copy config\config.example.json config\config.json
   ```
   Si el usuario o la contraseña de MySQL son distintos de `root` sin contraseña, ajustarlos en `config/config.json`.
5. En phpMyAdmin, pestaña **SQL**, ejecutar el contenido de `database/schema.sql`. Este script crea la base `donatrack_db` y todas las tablas.
6. (Opcional) Ejecutar `database/datos_prueba.sql` para cargar donantes e insumos de ejemplo. Sin donantes no se puede registrar un ingreso.
7. Abrir `http://localhost/donatrack/public/index.php`.

## Ambientes

La aplicación reconoce tres ambientes mediante el campo `environment` de `config/config.json`, que se expone en toda la app como la constante `APP_ENV`:

| Ambiente | Comportamiento ante un error |
|---|---|
| `DEVELOPMENT` | Muestra el detalle técnico en una caja de debug |
| `TESTING` | Igual que DEVELOPMENT |
| `PRODUCTION` | Muestra un mensaje amigable y guarda el error real, con fecha y hora, en `logs/sistema_error.log` |

## Modelo de datos

El enunciado define 3 entidades (Insumos, Donantes, Despachos). Se agregaron 2 tablas intermedias para modelar las relaciones entre ellas sin duplicar información:

- **insumos**: material, categoría y stock actual (con restricción para que nunca sea negativo).
- **donantes**: nombre o razón social, email único y teléfono.
- **despachos**: cabecera de cada entrega (número de remito autoincremental, fecha y hora, destino).
- **detalle_despachos**: qué insumos y qué cantidades incluye cada despacho (un despacho puede tener varios).
- **ingresos**: cada donación recibida: qué donante entregó qué insumo, en qué cantidad y cuándo.

## Funcionalidades

- **Inicio** (`index.php`): accesos directos a las secciones.
- **Registrar ingreso / donación** (`ingreso.php`): suma stock a un insumo existente o da de alta un insumo nuevo, asociado a un donante. Todo se guarda en una transacción.
- **Registrar despacho** (`despacho.php`): descuenta stock y valida de forma estricta que haya suficiente antes de confirmar. Si no alcanza, lanza una excepción propia (`StockInsuficienteException`) y, en `PRODUCTION`, el evento queda en el log. Al confirmar, ofrece descargar el remito en PDF.
- **Listar inventario** (`inventario.php`): categorías y cantidades actuales.
- **Historial de logs** (`logs.php`): eventos registrados en `sistema_error.log`, del más reciente al más antiguo.
- **Remito en PDF** (`remito.php`): generado con DomPDF a partir del número de remito.

## Limitaciones conocidas

- Cada despacho incluye un solo insumo desde la interfaz. El modelo de datos (`detalle_despachos`) ya admite varios por despacho.
- Los donantes se cargan directamente en la base de datos, ya que el enunciado no pide una pantalla para gestionarlos.