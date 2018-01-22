<?php

const WP_CONFIG = 'wp-config.php';
const CPANEL_USER = '/var/cpanel/users/';
const WPCONFIG = 'wp-config.php';

function checkCPanelIsNotNull($cpanel)
{
    return $cpanel instanceof CPANEL;
}

function getDomains(CPANEL $cpanel)
{
    /* Esta funciona devuelve un array con los dominios, subdominios de la cuenta excepto los alias o dominios aparcados, si hay algún problema devuelve NULL
     * Como parámetro acepta un objeto de tipo Cpanel
     */
    $domains = [];
    $domainsAccount = getDomainsApiCpanel($cpanel);

    //Dominio principal de la cuenta
    $mainDomain = $domainsAccount["cpanelresult"]["result"]["data"]["main_domain"];
    $domains[] = $mainDomain;

    //Dominios adicionales
    $addonDomains = $domainsAccount["cpanelresult"]["result"]["data"]["addon_domains"];
    for ($i = 0; $i < count($addonDomains); $i++) {
        $domains[] = $addonDomains[$i];
    }
    //Subdominios
    $subdomains = $domainsAccount["cpanelresult"]["result"]["data"]["sub_domains"];
    for ($i = 0; $i < count($subdomains); $i++) {
        $domains[] = $subdomains[$i];
    }

    return $domains;
}

function getDomainsApiCpanel($cpanel)
{
    return $cpanel->uapi('DomainInfo', 'list_domains');
}

function getQuotasApi($cpanel)
{
    return $cpanel->uapi('Quota', 'get_quota_info');
}

function getDomainInfoApi($cpanel, $domain)
{
    return $cpanel->uapi('DomainInfo', 'single_domain_data', ['domain' => $domain,]);
}

function getDomainsWordpress($cpanel, $domains)
{
    $domainsWordpress = [];
    for ($i = 0; $i < count($domains); $i++) {
        if (isDomainWordpress($cpanel, $domains[$i])) {
            $domainsWordpress [] = $domains[$i];
        }
    }

    return $domainsWordpress;
}

function getQuotas($cpanel)
{
    $quotaInfo = getQuotasApi($cpanel);
    return [
        "space_free" => $quotaInfo["cpanelresult"]["result"]["data"]["megabytes_remain"],
        "space_total" => $quotaInfo["cpanelresult"]["result"]["data"]["megabyte_limit"],
        "space_used" => $quotaInfo["cpanelresult"]["result"]["data"]["megabytes_used"],
    ];
}
function getDocumentrootDomain($cpanel, $domain)
{
    $domainInfo = getDomainInfoApi($cpanel, $domain);
    return $domainInfo["cpanelresult"]["result"]["data"]["documentroot"];
}

/**
 * @param $cpanel
 * @param $domain
 * @return mixed
 */

function isDomainWordpress($cpanel, $domain)
{
    $path = getDocumentrootDomain($cpanel, $domain);
    $pathWpconfig = getDocumentrootDomain($cpanel, $domain)."/".WP_CONFIG;
    $directorys = ["wp-admin", "wp-includes", "wp-content"];
    $wpDirectory = array_map(function ($value) use ($path) {
        return $path."/".$value;
    }, $directorys);

    return (is_file($pathWpconfig) &&
            is_dir($wpDirectory[0]) &&
            is_dir($wpDirectory[1]) &&
            is_dir($wpDirectory[2]));
}
function getAccountName($cpanel)
{
    return $cpanel->cpanelprint('$user');  //Devuelve el nombre de la cuenta
}

function getPackage($account)
{
    $fileCpanel = fopen(CPANEL_USER.$account, 'r') or die($php_errormsg);
    while (!feof($fileCpanel)) {
        $line = fgets($fileCpanel, 4096);
        if (preg_match('/PLAN=(.*)/', $line, $match)) {
            break;
        }
    }
    fclose($fileCpanel);
    return $match[1];
}
function getNumMysql($cpanel)
{
    $dbinfo = $cpanel->api2('MysqlFE', 'getalldbsinfo');
    $dbs = array_column($dbinfo["cpanelresult"]["data"],
        'db');
    return count($dbs);
}
function check_web($domain)
{
    //Esta función comprueba si una web da 200 ok.
    $url = "http://"."$domain";
    //$url = "http://webempresa.com";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $rt = curl_exec($ch);
    $info = curl_getinfo($ch);
    return $info["http_code"] == 200;
}
function getSpaceWeb($cpanel, $domain)
{
    //Calcula el tamaño de la web a clonar
    $path = getDocumentrootDomain($cpanel, $domain);
    $wpcontentSpace = exec("du -sh --block-size=M $path/wp-content | sed 's/M//g' | awk {'print $1}'");
    $wpadminSpace = exec("du -sh --block-size=M $path/wp-admin | sed 's/M//g' | awk {'print $1}'");
    $wpincluesSpace = exec("du -sh --block-size=M $path/wp-includes | sed 's/M//g' | awk {'print $1}'");

    return $wpcontentSpace + $wpadminSpace + $wpincluesSpace;
}

function get_variables_user($cpanel)
{
    echo "Máximo número de Dominios adicionales :".$cpanel->cpanelprint('$CPDATA{\'MAXADDON\'}');
    echo "<br>";
    echo "Máximo numero de subdominios: ".$cpanel->cpanelprint('$CPDATA{\'MAXSUB\'}');
    echo "<br>";
    echo "Máximo numero de Dominios adicionales: ".$cpanel->cpanelprint('$CPDATA{\'MAXADDON\'}');
    echo "<br>";
    echo "Máximo número de bases de datos: ".$cpanel->cpanelprint('$CPDATA{\'MAXSQL\'}');

    echo "<br>";
    //var_dump($cpanel->cpanelprint('$version'));
    //var_dump($cpanel->cpanelprint('$abshomedir'));
    //print_r  ( $cpanel->fetch('$CPDATA{\'CONTACTEMAIL\'}') );

//    var_dump($user);

}

function getNumSubdomains($cpanel)
{
    // Los subdominios son los dominios adicionales mas subdominios propiamente dicho menos el MainDomain
    return count(getDomains($cpanel)) - 1;
}

function check_space($cpanel, $domain)
{
    $webSpace = getSpaceWeb($cpanel, $domain);
    $quota_acct = getQuotas($cpanel);

    return $quota_acct["space_free"] == "0.00" || $quota_acct["space_free"] > $webSpace;
}

function createDb($cpanel, $name)
{
    //La api devuelve 0 en la key result si falla la creación db, o sino la key result no existe
    $result = $cpanel->api2('MysqlFE', 'createdb',
        array('db' => $name,)
    );
    return ! isset($result['cpanelresult']['data']['result']);
}
function createUserdb($cpanel, $username, $password)
{
    $result = $cpanel->api2('MysqlFE', 'createdbuser',
        array(
            'dbuser'   => $username,
            'password' => $password,
        )
    );
    return ! isset($result['cpanelresult']['data']['result']);
}
function grantUserDb($cpanel, $dbname, $userdb)
{
    $return = $cpanel->api2('MysqlFE', 'setdbuserprivileges',
        array(
            'privileges' => 'ALL PRIVILEGES',
            'db' => $dbname,
            'dbuser' => $userdb,
        )
    );
    return ! isset($result['cpanelresult']['data']['result']);
}
function createNameRandom($length)
{
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));
    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}
function dd($variable)
{
    echo "<pre>";
    die(var_dump($variable));
    echo "</pre>";
}
function checkTestDomain($cpanel, $domainSelect)
{
    $domainsAcct = getDomains($cpanel);
    $subdomainClon = '';
    $result = [];

    if (! in_array($domainSelect, getDomainsWordpress($cpanel, $domainsAcct)))  {
        return getMessage(false, "El dominio elegido no pertenece a la cuenta o no es Wordpress");
    }
    if (in_array($subdomainClon, $domainsAcct)) {
        $result["check"] = false;
        $result["error_txt"] = "El subdominio $subdomainClon ya existe, por favor elimínalo e intenta de nuevo";
        return $result;
    }
}
function getMessage($test , $message)
{
    $result = [];
    $result["check"] = $test;
    $result["error_txt"] = $message;
    return $result;

}

