<?php
/**
 * Created by PhpStorm.
 * User: jose
 * Date: 8/12/17
 * Time: 22:48
 */

class Domain
{
    private $cpanel;
    private $name;
    const WP_CONFIG = 'wp-config.php';

    public function __construct($domain, CPANEL $cpanel)
    {
        $this->setName($domain);
        $this->cpanel = $cpanel;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDocumentRoot()
    {
        return $this->getDocumentRootApi(){["cpanelresult"]["result"]["data"]["documentroot"]};
    }

    protected function getDocumentRootApi()
    {
        return $this->cpanel->uapi('DomainInfo', 'single_domain_data', ['domain' => $this->name,]);
    }

    public function isWordpress()
    {
        $directory = ["wp-admin", "wp-includes", "wp-content"];
        //usamos array_map para añadir el string del path a cada directorio del wordpress.
        $wp_directory = array_map(function ($value) use ($path) {
            return $path . "/" . $value;
        }, $directory);

        return (is_file($this->PathWpconfig())
                && is_dir($wp_directory[0])
                && is_dir($wp_directory[1])
                && is_dir($wp_directory[2]));
    }

    protected function PathWpconfig()
    {
        return $this->getDocumentRoot() . WP_CONFIG;
    }

    public function isOnline()
    {
        //Esta función comprueba si una web da 200 ok.
        $url = "http://" . $this->name;
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

    

}