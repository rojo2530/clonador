<?php
if(session_status() == 1) {
    session_start();
}
define('AJAX_REQUEST', isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (! AJAX_REQUEST) {
    die('No debes de acceder a este fichero');
}
require_once ("/usr/local/cpanel/php/cpanel.php");
require_once '/usr/local/cpanel/base/frontend/paper_lantern/clonador/functions.php';
$config = require_once '/usr/local/cpanel/base/frontend/paper_lantern/clonador/config.live.php';
$cpanel = new CPANEL();

$config["domain"] = $_SESSION["dominio_elegido"] ?: '';
/* if (isset($_SESSION["dominio_elegido"])) {
    $config["domain"] = $_SESSION["dominio_elegido"];
} else {
    $config["domain"] = '';
} */
$nameDbAndUser = getPrefixBd($cpanel).'_'.createNameRandom(4);
$config['dbname'] = $nameDbAndUser;
$config['userdb'] = $nameDbAndUser;
$config['userdbPassword'] = createNameRandom(8);
if (! createDb($cpanel, $nameDbAndUser)) {
    dd('No se ha podido crear la base de datos');
}
if (! createUserdb($cpanel, $nameDbAndUser, $config['userdbPassword'])) {
    dd('No se ha podido crear el usuario de la base de datos');
}
grantUserDb($cpanel, $config['dbname'], $config['userdb']);
if (! createSubdomain($cpanel, $config['prefixSubdomain'], $config['domain'])) {
    dd('No se ha podido crear el subdominio');
}
$paramScript = [
    'account' => getAccountName($cpanel),
    'domain' => $config['domain'],
    'subdomainClon' => $config['prefixSubdomain'].'.'.$config['domain'],
    'dbname' => $config['dbname'],
    'userdb' => $config['userdb'],
    'userdbPassword' => $config['userdbPassword'],
];
echo json_encode($paramScript);

