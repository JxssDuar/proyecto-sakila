# proyecto-sakila

## Despliegue en Railway

La imagen usa PHP 8.2 con Apache, `mpm_prefork`, `pdo_mysql` y `mysqli`.
`mpm_event` y `mpm_worker` se deshabilitan antes de habilitar `mpm_prefork`.
El proceso de inicio es `apache2-foreground` y Apache escucha en el puerto 80.
`railway.toml` selecciona el Dockerfile de la raíz y ese comando de inicio.

1. Sube los cambios del Dockerfile a la rama conectada al servicio de Railway.
2. En el servicio, abre **Settings** y confirma que el repositorio y la rama
   sean los correctos. Deja **Root Directory** en la raíz del repositorio (`/`).
3. Si existe la variable `RAILWAY_DOCKERFILE_PATH`, usa `Dockerfile` como valor.
   Confirma también que **Dockerfile Path**, si está configurado, sea `Dockerfile`.
4. Confirma que Railway lea `/railway.toml` como archivo de configuración.
   En los detalles del despliegue, el constructor debe ser `DOCKERFILE`, la
   ruta `Dockerfile` y el comando de inicio `apache2-foreground`. No uses un
   comando que instale Apache, habilite módulos o lo inicie en segundo plano.
5. En **Variables**, configura `PORT=80`. En **Networking**, configura el
   puerto de destino del dominio como `80`.
6. Para `conexion.php`, configura `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`
   y `DB_NAME` con los datos del servicio MySQL que contiene Sakila. Introduce
   esos valores únicamente en Railway; no los guardes en el repositorio.
7. Inicia un nuevo despliegue y abre los logs de construcción. Debe aparecer
   `Using detected Dockerfile!`. En una construcción sin caché, las
   comprobaciones del Dockerfile deben mostrar:

   ```text
   Syntax OK
   mpm_prefork_module (shared)
   PDO Driver for MySQL => enabled
   Railway-Sakila: root Dockerfile validated
   ```

   No deben aparecer `mpm_event_module` ni `mpm_worker_module` en la lista de
   módulos activos. La construcción falla si existe más de un MPM, si el MPM
   activo no es prefork o si faltan las extensiones MySQL.
8. Abre los logs de ejecución. Apache debe mostrar mensajes equivalentes a:

   ```text
   [mpm_prefork:notice] AH00163: Apache/... PHP/... configured -- resuming normal operations
   [core:notice] AH00094: Command line: 'apache2 -D FOREGROUND'
   ```

   No debe aparecer `AH00534: More than one MPM loaded`. El aviso `AH00558`
   sobre `ServerName`, si aparece, no es el error de MPM y no detiene Apache.

El Dockerfile está en la raíz, pero confirmar qué versión construye Railway
requiere revisar el despliegue remoto, su rama y sus logs.

**Limitación de la aplicación existente:** las páginas PHP usan `mysqli` con
conexiones propias a `localhost` y credenciales escritas en el código. No usan
`conexion.php`. Este ajuste de Apache no cambia esas páginas ni hace que usen
las variables `DB_*`. El arranque de Apache y la conexión de esas páginas al
MySQL remoto son comprobaciones distintas. Las credenciales existentes deben
retirarse del código y reemplazarse si siguen siendo válidas.

`conexion.local.php` y `.env` permanecen excluidos mediante `.gitignore` y
`.dockerignore`.

La validación de ejecución debe realizarse con los logs del nuevo despliegue
de Railway. Una revisión estática del Dockerfile no demuestra que el servicio
remoto ya haya arrancado correctamente.
