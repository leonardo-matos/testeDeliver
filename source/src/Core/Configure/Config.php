<?php
namespace API\Core\Configure;

GLOBAL $SISCONF;

$SISCONF['API']['URL_BASE'] = "http://localhost/api/source";
// $SISCONF['API']['URL_BASE'] = "http://10.62.7.248/api/source";

GLOBAL $ARRLOG;
$ARRLOG	= array();


class Config
{

	public $dsn = 'mysql:dbname=deliver;host=localhost';
	public $username = 'root';
	public $password =  '';	
	
	public $cacheEnabled	= true;

	public $cacheTime	= 3600;
	
	public function getRequestURL($param='')
	{
		
		$protocol	= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT']	== 443) ? "https://" : "http://";
		$domainName	= $_SERVER['HTTP_HOST'];
		$url	= parse_url($_SERVER['REQUEST_URI']);
		return $protocol.$domainName.$url['path'].(!empty($param)?'/'.$param:'');

	}
	
	public function getCacheConfig()
	{
		return array(
			"storage" 	=>  "files",
			"overwrite"	=>  "files",
			"caching_method"	=> 2,
			"htaccess"	=>  true, // .htaccess protect
		);
	}

	public static function isDeveloper()
	{

		$ipAccessMachine	= $_SERVER['REMOTE_ADDR'];
		$arrIpsDevelopers	= array();
		
		$arrIpsDevelopers[]	= '::1';
		$arrIpsDevelopers[]	= '172.23.240.1';

		if(in_array($ipAccessMachine,$arrIpsDevelopers)){
			return true;
		}

		return false;
	}

}