# Dona-Track ONG

Autor: [Bulacio Ezequiel]
Trabajo final para Alumno Libre, Proyecto Informático (UPSO).

## De qué trata

Dona-Track es una página web para una ONG que recibe donaciones y las entrega a centros comunitarios. Sirve para anotar lo que llega, ver cuánto stock hay de cada cosa y registrar lo que se entrega, con un remito en PDF.

## Qué usé

- PHP con PDO y MySQL
- Bootstrap 5 para el diseño
- DomPDF (instalado con Composer) para hacer los remitos en PDF
- Git y GitHub

## Cómo probarlo

Necesitás XAMPP y Composer.

1. Clonar el repositorio dentro de `htdocs`:
   ```
   git clone https://github.com/ezequielbulacio/donatrack.git
   ```
2. Instalar las librerías:
   ```
   composer install
   ```
3. Prender Apache y MySQL desde XAMPP.
4. Copiar el archivo de configuración:
   ```
   copy config\config.example.json config\config.json
   ```
   Si tu MySQL tiene otra contraseña, cambiala en `config/config.json`.
5. En phpMyAdmin, ir a la pestaña SQL y ejecutar `database/schema.sql`. Ese archivo crea la base `donatrack_db` y las tablas.
6. Opcional: ejecutar `database/datos_prueba.sql` para cargar algunos donantes e insumos de ejemplo. Hace falta, porque sin donantes no se puede registrar un ingreso.
7. Entrar a `http://localhost/donatrack/public/index.php`.

## Los tres ambientes

En `config/config.json` se elige el ambiente (`DEVELOPMENT`, `TESTING` o `PRODUCTION`). Cambia lo que pasa cuando hay un error:

- **DEVELOPMENT y TESTING:** el error se muestra en pantalla, en una caja de debug.
- **PRODUCTION:** el usuario ve un mensaje simple y el error real se guarda, con fecha y hora, en `logs/sistema_error.log`.

## Base de datos

El enunciado pedía tres tablas (insumos, donantes y despachos). Terminé haciendo cinco, porque necesitaba guardar cada movimiento por separado:

- **insumos:** qué material es, su categoría y cuánto stock hay.
- **donantes:** nombre o razón social, email (no se puede repetir) y teléfono.
- **despachos:** cada entrega, con su número de remito, fecha y destino.
- **detalle_despachos:** qué insumos y cuántos van en cada despacho.
- **ingresos:** cada donación recibida: quién donó, qué y cuánto.

## Qué se puede hacer

- **Registrar ingreso:** sumar stock a un insumo o cargar uno nuevo, indicando quién lo donó.
- **Registrar despacho:** descuenta stock. Si se pide más de lo que hay, no deja hacerlo y avisa. Después de un despacho se puede descargar el remito en PDF.
- **Inventario:** lista lo que hay y cuánto.
- **Historial de logs:** muestra los errores guardados.

## Posibles mejoras

- Permitir cargar varios insumos en un mismo despacho. La base de datos ya lo soporta (tabla `detalle_despachos`), solo habría que adaptar el formulario.
- Agregar una pantalla para gestionar los donantes, que hoy se cargan directo en la base de datos.