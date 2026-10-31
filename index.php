<?php

/**
 * Marilú Díaz Editorial - Front Controller Fallback para Hosting Compartido (Hostinger / cPanel)
 * Si el servidor web no procesa la regla .htaccess, este archivo despacha la aplicación
 * directamente a través del controlador frontal seguro en public/index.php.
 */

require_once __DIR__ . '/public/index.php';
