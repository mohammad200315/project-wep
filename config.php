<?php
// config.php - ملف الإعدادات العامة الموحد

define('ENVIRONMENT', 'development');
define('SITE_NAME', 'لوحة تحكم الميزانية');
define('SITE_URL', 'http://localhost/project_db_advance/');
define('TIMEZONE', 'Asia/Riyadh');

// إعدادات قاعدة البيانات - تم التعديل لتوافق مشروعك
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'project_db_advance'); 

define('DEBUG_MODE', ENVIRONMENT === 'development');
date_default_timezone_set(TIMEZONE);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// دالة تنظيف المدخلات
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}