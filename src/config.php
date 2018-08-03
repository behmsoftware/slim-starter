<?php
/*
 * php settings
 */
ini_set('file_uploads', 'off');
ini_set('display_errors', 'off');
ini_set("error_reporting", E_WARNING | E_ERROR | E_DEPRECATED);

/*
 * const
 */
// page url
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define("SITE_URL", "http://localhost" . $_SERVER['REQUEST_URI']);
} else {
    define("SITE_URL", '/public/');
}

// page title
define("SITE_TITLE", "immosoft");
define("DB_PRAEFIX", "is_");
define("YEAR", "2018");
