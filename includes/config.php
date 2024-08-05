<?php
// Configurazioni generali
define('SITE_NAME', 'Bar App');

// Definizione delle costanti di percorso
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
define('VIEW_PATH', ROOT_PATH . 'views/');
define('PUBLIC_PATH', ROOT_PATH . '');
define('ASSET_PATH', ROOT_PATH . 'assets/');
define('ADMIN_PATH', ROOT_PATH . 'admin/');
define('MODEL_PATH', ROOT_PATH . 'models/');
define('CONTROLLER_PATH', ROOT_PATH . 'controllers/');
define('UPLOAD_PATH', ROOT_PATH . 'uploads/');

// Configurazione database
define('DB_HOST', 'localhost');
define('DB_NAME', 'bar_app');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Altre configurazioni
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'password123');

// Avvio sessione
session_start();
?>
