<?php
//prueba de commit
session_start();
require_once "/usr/local/cpanel/php/cpanel.php";
require_once "functions.php";

$cpanel = new CPANEL();
echo $cpanel->header("Clona tu Wordpress");
if (createSubdomain($cpanel, 'weclon', 'ppdesanfernando.es')) {
    echo "Se ha creado el subdomnio";
    die();
} else {
    echo "No se ha creado el subdominio";
    die();
}

$domainsWp = getDomainsWordpress($cpanel, getDomains($cpanel));
$nameDbAndUser = getAccountName($cpanel) . '_' . createNameRandom(4);

require 'views/index.view.php';

//echo $cpanel->footer("Webempresa");
$cpanel->end();



