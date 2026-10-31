# Guía de Despliegue en Hostinger

Esta guía detalla los pasos para desplegar la plataforma editorial de **Marilú Díaz** en un hosting compartido o Cloud de **Hostinger**.

---

## 1. Configuración de PHP en Hostinger (hPanel)

1. Ingresa a tu panel de control de Hostinger (**hPanel**).
2. En la barra de búsqueda, escribe **Configuración de PHP** (PHP Configuration).
3. Selecciona la versión **PHP 8.3**.
4. En la pestaña **Extensiones de PHP**, asegúrate de que estén activas:
   - `intl` (Obligatoria para CodeIgniter 4)
   - `gd` (Para conversión automática a WebP y miniaturas)
   - `mbstring`
   - `mysqli`
   - `fileinfo`
   - `curl`
   - `zip`
5. Guarda los cambios.

---

## 2. Creación de la Base de Datos MariaDB

1. En hPanel, ve a **Bases de Datos MySQL**.
2. Crea una nueva base de datos (por ejemplo `uXXXX_marilu`).
3. Asigna un usuario y una contraseña segura.
4. Anota el nombre de la base, usuario y contraseña.

---

## 3. Estructura de Archivos Segura (Fuera de la Raíz Pública)

Para máxima seguridad en servidores Apache/cPanel/Hostinger, el código fuente del backend debe residir fuera del acceso público directo:

```text
/home/uXXXX/
├── marilu_app/                  <-- Toda la aplicación excepto la carpeta public
│   ├── app/
│   ├── vendor/
│   ├── writable/
│   ├── .env
│   └── spark
└── domains/tudominio.com/public_html/   <-- Contenido de la carpeta public
    ├── index.php
    ├── assets/
    │   ├── brand/
    │   └── build/
    ├── uploads/
    └── .htaccess
```

### Ajuste de `index.php`
En el archivo `public_html/index.php`, actualiza la ruta al archivo `Paths.php`:
```php
require '/home/uXXXX/marilu_app/app/Config/Paths.php';
```

---

## 4. Configuración del archivo `.env` en Producción

Copia el archivo `.env.example` a `.env` en la carpeta `marilu_app`:

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://tudominio.com/'
app.indexPage = ''
app.forceGlobalSecureRequests = true

database.default.hostname = localhost
database.default.database = uXXXX_marilu
database.default.username = uXXXX_usuario
database.default.password = TuPasswordSeguro
database.default.DBDriver = MySQLi
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci

# Credenciales de Google para lectores (obtenidas en Google Cloud Console)
google.clientId = 'tu-client-id.apps.googleusercontent.com'
google.clientSecret = 'tu-client-secret'
```

---

## 5. Migraciones y Permisos de Escritura

Conéctate por SSH a Hostinger y ejecuta:

```bash
cd /home/uXXXX/marilu_app
php spark migrate --all
php spark db:seed EditorialSeeder
chmod -R 775 writable
chmod -R 775 /home/uXXXX/domains/tudominio.com/public_html/uploads
```

Si tu plan no incluye SSH, puedes exportar la base de datos local desde phpMyAdmin e importarla directamente en el phpMyAdmin de Hostinger.

---

## 6. Checklist de Seguridad Obligatoria Pre-Lanzamiento (10 Puntos Blindados)

Antes de abrir el sitio al público general, valida el cumplimiento de las siguientes 10 medidas de seguridad en Hostinger:

1. **HTTPS / Candado SSL en toda la web:**
   - En hPanel ve a **Seguridad > Certificados SSL** y activa el certificado SSL gratuito (Let's Encrypt con renovación automática).
   - En `.htaccess` y en `.env` ya está configurado `forceGlobalSecureRequests = true` y la redirección 301 automática para impedir accesos HTTP no encriptados.

2. **Protección WAF y Cabeceras Seguras:**
   - Hostinger cuenta con cortafuegos perimetral y Cloudflare integration.
   - CodeIgniter envía cabeceras seguras activas (`X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: same-origin`) y protección CSRF en el 100% de los formularios.

3. **Registros de Correo contra Ataques (SPF, DKIM, DMARC):**
   - En hPanel > **Correos > Administrador de DNS**, asegúrate de que los registros TXT de **SPF**, **DKIM** y **DMARC** estén activos para que ningún atacante pueda suplantar la identidad de tu dominio enviando spam a tu nombre.

4. **Protección Anti-Bots (Honeypot):**
   - El filtro `Honeypot` de CodeIgniter 4 está activo globalmente en `app/Config/Filters.php`. Bloquea automáticamente robots de spam que intentan enviar formularios sin molestar a los usuarios con captchas invasivos.

5. **Bloqueo de Fuerza Bruta en Login:**
   - La plataforma incluye el filtro `LoginLockout` (`app/Filters/LoginLockout.php`) y `auth-rates`. Si una IP falla 5 veces seguidas su contraseña, el acceso queda bloqueado automáticamente durante 15 minutos y se registra en el log del sistema.

6. **Escáner de Virus y Malware Activo:**
   - Hostinger cuenta con **Imunify360** activo de fábrica, ejecutando escaneos diarios automáticos contra malware y webshells.
   - En la aplicación, todo archivo multimedia pasa por `MediaService`: se comprueba el MIME real con `finfo` y se recodifica con GD a formato `.webp`, destruyendo cualquier script ejecutable incrustado.

7. **Arquitectura sin WordPress (Menor Superficie de Ataque):**
   - El proyecto está desarrollado a medida sobre **PHP 8.3 + CodeIgniter 4 oficial**. No existe código de WordPress ni plugins de terceros vulnerables, eliminando los vectores de ataque más comunes de la web.

8. **Cero Plugins Piratas (100% Clean Code):**
   - No se utiliza ningún componente nulled o descargado de fuentes no oficiales. Todas las dependencias son paquetes de código abierto legítimos descargados mediante Composer y NPM desde sus repositorios oficiales.

9. **Acceso al Panel Ofuscable (Sin /wp-admin):**
   - No existe `/wp-admin`. Además, la URL del panel administrativo es personalizable en el `.env` mediante la directiva `ADMIN_PATH = 'gestion-editorial'` o cualquier nombre de tu preferencia.

10. **Copias de Seguridad (Backups Periódicos):**
    - En hPanel ve a **Archivos > Copias de seguridad**. Hostinger genera respaldos diarios automáticos de la base de datos y archivos con restauración en 1 clic.
    - Se recomienda descargar periódicamente una copia local desde phpMyAdmin de Hostinger para contar con respaldo fuera del servidor.

