<?php
if(session_status() == 1) {
    session_start();
}
define('AJAX_REQUEST', isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (! AJAX_REQUEST) {
    die('No debes de acceder a este fichero');
}
echo "<pre>";
var_dump($_SERVER);
echo "</pre>";
require_once ("/usr/local/cpanel/php/cpanel.php");
require_once '/usr/local/cpanel/base/frontend/paper_lantern/clonador/functions.php';
$config = require '/usr/local/cpanel/base/frontend/paper_lantern/clonador/config.live.php';

$cpanel = new CPANEL();
$domainsAcct = getDomains($cpanel);
$domainsWp = getDomainsWordpress($cpanel, getDomains($cpanel));
$subdomainsNum = getNumSubdomains($cpanel);
$numDbs = getNumMysql($cpanel);
$max_subdomains = $cpanel->cpanelprint('$CPDATA{\'MAXSUB\'}');  //Puede ser un número o bien unlimited
$max_dbs = $cpanel->cpanelprint('$CPDATA{\'MAXSQL\'}'); //Puede ser un número o bien unlimited


$domainSelect = $_POST['dominio'];
$subdomainClon = $config['prefixSubdomain'].'.'.$domainSelect;   //weclon.domain.com

$_SESSION["dominio_elegido"] = $domainSelect;
$_SESSION["document_root"] = getDocumentrootDomain($cpanel, $domainSelect);
$_SESSION["all_check"] = false;
$result = [];
// $result = checkTestDomain($cpanel, $domainSelect);
if (! in_array($domainSelect, $domainsWp))  {
    $result["check"] = false;
    $result["error_txt"] = "El dominio elegido no pertenece a la cuenta o no es Wordpress";
} else if (in_array($subdomainClon, $domainsAcct)) {
    $result["check"] = false;
    $result["error_txt"] = "El subdominio $subdomainClon ya existe, por favor elimínalo e intenta de nuevo";
} else if ($max_subdomains != 'unlimited' && $max_subdomains <= $subdomainsNum) {
    $result["check"] = false;
    $result["error_txt"] = "No puedes clonar tu Wordpress porque tu cuenta no tiene permisos para crear un subdominio";
} else if ($max_dbs != 'unlimited' && $max_dbs <= $numDbs) {
    $result["check"] = false;
    $result["error_txt"] = "No puedes clonar tu Wordpress porque tu cuenta no tiene permisos para crear un base de datos";
} else if (! check_web($domainSelect)) {
    $result["check"] = false;
    $error_txt =  "La web no funciona correctamente o no da un 200 OK: ";
    $result["error_txt"] = $error_txt;
} else if (!check_space($cpanel, $domainSelect)) {
    $result["check"] = false;
    $result["error_txt"] = "No tienes espacio suficiente para clonar tu web";
} else {
    $result["check"] = true;
    $result["error_txt"] = "Todos los test pasado correctmente, se va a clonar tu web $domainSelect en el subdominio $subdomainClon en la cuenta ". $_SESSION['account'];
    $_SESSION["all_check"] = true;
}

$cpanel->end();
echo json_encode($result);

