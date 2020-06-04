<?php
/* Konfigurasi php.ini */
date_default_timezone_set("Asia/Jakarta");
session_save_path("sessions");
ini_set("error_log", "Errors.log.txt");
ini_set('memory_limit', '1G');
set_time_limit(0);
ob_start();
session_start();


/* Penyertaan file pustaka*/
include "system/conf.php";
include "system/_debug.php";
include "system/_viewstate.php";
include "system/init.php";
include "system/router.php";
include "system/aclcheck.php";

/* template and output */
if (file_exists(MODULE_TEMPLATE_DIR . DEFAULT_TEMPLATE)) { // gunakan module template jika ada
    $base_template = MODULE_TEMPLATE_DIR . DEFAULT_TEMPLATE;
} else if (file_exists(SITE_TEMPLATE . DEFAULT_TEMPLATE)) { // gunakan site template sebagai default
    $base_template = SITE_TEMPLATE . DEFAULT_TEMPLATE;
} else {
    $base_template = false;
}
if ($base_template) {
    $_out = xliteTemplate($base_template, '_absolute');
}

/* filter & execute */
if (file_exists(MODULE_DIR . "__filter.php"))
    require_once(MODULE_DIR . "__filter.php");
	$scripts      = array(
    SITE_MODULE . "__pre.php",
    MODULE_DIR . "__init.php",
    MODULE_DIR . "__pre.php",
    $script_name,
    MODULE_DIR . "__post.php",
    MODULE_DIR . "__tests.php",
    SITE_MODULE . "__post.php"
);

$_webservices = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$main_content = executeScript($scripts);
if ($base_template && !defined('RAW_OUTPUT')) {
	$_out->assign('muatan', $main_content);
	$_out->renderToScreen();
} else {
    if (defined('AJAX_OUTPUT')) {
        $_ajax->setContent($main_content);
        echo $_ajax->getJSON();
    } else {
        echo $main_content;
    }
}

?>