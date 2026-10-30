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

