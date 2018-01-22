<?php

require_once ("Domain.php");

/**
 * Created by PhpStorm.
 * User: jose
 * Date: 15/11/17
 * Time: 0:*/
class Account
{
    private $cpanel; // Objeto de tipo cPanel
    private $name;
    const FILE_CONF = '/var/cpanel/users/';
    private $mainDomain;
    private $addonDomanains = array ();
    private $subdomains = array ();
    private $allDomainsApi;

    public function __construct(CPANEL $cpanel)
    {
        //En el constructor instanciamos uno objeto de tipo Cpanel para poder llamar a la APi de cpanel en los metodos.
        $this->cpanel = $cpanel;
        $this->allDomainsApi = $this->getDomainsApi(); //Nos traemos todos los dominios via APi CPanel
    }

    protected function getDomainsApi ()
    {
        return $this->cpanel->uapi('DomainInfo', 'list_domains');
    }

    public function getName()
    {
        return $this->cpanel->cpanelprint('$user');
    }

    public function getPackage()
    {
        $file = FILE_CONF . $this->getName();
        $fh = fopen($file, 'r') or die($php_errormsg);
        while (! feof($fh)) {
            $line = fgets($fh, 4096);
            if (preg_match('/PLAN=(.*)/', $line, $match)) {
                break;
            }
        }
        fclose($fh);

        return $match[1];
    }

    public function getMainDomain()
    {
        $mainDomain = $this->allDomainsApi["cpanelresult"]["result"]["data"]["main_domain"];
        $this->mainDomain = new Domain ($mainDomain, $this->cpanel);

        return $this->mainDomain;
    }

    public function getAddonDomains()
    {
        $addonDomains = $this->allDomainsApi["cpanelresult"]["result"]["data"]["addon_domains"];
        for ($i = 0; $i < count($addonDomains); $i++) {
                $this->addonDomanains[] = new Domain ($addonDomains[$i], $this->cpanel);
        }

        return $this->addonDomanains;
    }

    public function getSubdomains()
    {
        $subdomains = $this->allDomainsApi["cpanelresult"]["result"]["data"]["sub_domains"];
        for ($i = 0; $i < count($subdomains); $i++) {
            $this->subdomains[] = new Domain ($subdomains[$i], $this->cpanel);
        }
    }
}

